# Rencana fitur Numa-Edu & konsep Admin Ma'arif / Pengurus Cabang

Dokumen ini merangkum **rencana pengembangan fitur** yang masih bisa diimplementasikan, serta **konsep baru**: hierarki **super user tingkat Ma'arif / pengurus cabang** yang mengawasi banyak sekolah dalam satu sistem.

---

## 1. Konsep multi-lembaga & Admin Ma'arif (Pengurus Cabang)

### 1.1 Latar belakang

Sistem tidak hanya dipakai oleh **satu sekolah**, tetapi juga oleh **jaringan Ma'arif** (mis. tingkat PC / pengurus cabang) yang perlu:

- Memantau **data sekolah** yang menggunakan aplikasi (agregat atau per sekolah, sesuai kebijakan).
- Memantau **siswa, guru, pegawai** (read-only atau terbatas, sesuai kebijakan privasi & UU PDP).
- **Mengelola pendaftaran / akun operator sekolah** (admin sekolah pertama atau pengganti).
- Fitur tambahan yang bermanfaat: **dashboard cabang**, laporan lintas sekolah, status sinkronisasi, dsb.

### 1.2 Peran yang diusulkan

| Peran | Deskripsi singkat |
|--------|-------------------|
| **Super admin global** (opsional) | Pemilik platform / tim teknis; konfigurasi sistem. |
| **Admin Ma'arif / Pengurus cabang** | User PC/cabang; wilayah kerja = cabang tertentu; CRUD sekolah di bawahnya; undang/atur admin sekolah. |
| **Admin sekolah (operator)** | Mengelola data **satu lembaga** (NPSN, alamat, operator, dsb.) + operasional harian (siswa, guru, tagihan, …). |
| Peran existing | Guru, siswa, wali, dsb. — tetap scoped ke **sekolah** masing-masing. |

### 1.3 Pendaftaran sekolah oleh pengurus cabang

Pengurus cabang dapat **mendaftarkan** atau **mengaktifkan** sekolah baru dan mengisi / mengatur minimal:

**Data lembaga**

- NPSN (unik, identitas resmi sekolah)
- Nama lembaga
- Alamat lengkap
- Nomor telepon lembaga
- Email lembaga (kontak resmi)
- Website (opsional)
- **Kepala lembaga / sekolah** (nama, NIP opsional)
- **Akreditasi** (nilai / tahun, sesuai kebutuhan)
- Relasi ke **cabang / wilayah Ma'arif** (foreign key ke entitas `cabang` atau `wilayah`)

**Akun operator sekolah**

- Nama operator
- Email login operator (bisa berbeda dari email lembaga)
- Penugasan role `admin` (atau `admin_sekolah`) **hanya** untuk sekolah tersebut

### 1.4 Aturan login sekolah (NPSN)

**Konsep email login operator sekolah:**

- Format: `{NPSN}@numa.com`  
  - Contoh: NPSN `12345678` → `12345678@numa.com`  
- **Alasan:** NPSN unik nasional → email sintetis unik → mudah diingat dan tidak bentrok antar sekolah.
- **Implementasi teknis nanti:** domain `numa.com` harus bisa menerima email (MX) **atau** hanya dipakai sebagai **identifier login** (tanpa inbox) dengan verifikasi nomor HP / undangan link dari pengurus cabang.

Catatan produk:

- Boleh ada **override** email operator ke alamat domain sekolah jika kebijakan Ma'arif mengizinkan fleksibilitas.
- Reset password harus aman (token email alternatif atau SMS/WhatsApp ke operator).

### 1.5 Isolasi data (multi-tenant)

Setiap tabel operasional (siswa, guru, kelas, tagihan, …) harus memiliki **`sekolah_id`** (atau `lembaga_id`) sehingga:

- Admin sekolah hanya melihat data sekolahnya.
- Pengurus cabang melihat **hanya sekolah di cabangnya** (filter `sekolah_id IN (...)`).
- Super admin global melihat semua (jika ada).

Ini adalah **refactor besar** terhadap basis kode saat ini; direncanakan bertahap (lihat bagian prioritas implementasi di bawah).

### 1.6 Fitur tambahan yang bermanfaat untuk pengurus cabang (ide)

- Dashboard: jumlah sekolah aktif, total siswa/guru agregat, tagihan belum lunas agregat (opsional, sensitif).
- Daftar sekolah + status onboarding (profil lengkap / belum).
- Undang admin sekolah, nonaktifkan sekolah, reset akses operator.
- Ekspor laporan **lintas sekolah** (CSV) untuk keperluan PC (dengan audit log).
- Notifikasi ke PC jika ada insiden (mis. gagal login massal — opsional).

---

## 2. Rencana fitur produk (belum utuh / belum ada) — bisa diimplementasikan

Ringkasan dari diskusi roadmap; prioritas bisa disesuaikan kebijakan Ma'arif.

### 2.1 Akademik & penilaian

- **E-Rapor / rapor digital** (ringkasan per semester, narasi capaian, PDF, tanda tangan).
- **CBT / ujian online** (bank soal, sesi ujian, timer, koreksi otomatis PG).
- **KKM / rubrik & deskripsi** pada modul nilai.
- **Naik kelas / kelulusan massal** (bulk pindah kelas, arsip lulusan).

### 2.2 Keuangan & pembayaran

- **Gateway pembayaran** (Midtrans, Xendit, dll.) + webhook → pembayaran otomatis.
- **Kwitansi / invoice PDF** per tagihan atau per pembayaran.
- **Anggaran & realisasi** sederhana (pos keuangan).

### 2.3 Komunikasi & portal

- **Portal wali diperluas**: detail tagihan, unduh bukti, pengajuan/pertanyaan (opsional).
- **Pesan internal** (inbox antar peran), terpisah dari notifikasi sistem.
- **Kalender sekolah** (ujian, libur, kegiatan) + pengingat.

### 2.4 Operasional & SDM

- **Cuti / izin guru & pegawai** dengan alur persetujuan.
- **Surat menyurat** (nomor surat, template SK / keterangan siswa).
- **Perpustakaan** (buku, peminjaman, denda ringan).

### 2.5 Data & integrasi

- **Integrasi EMIS / Dapodik** (sinkron atau export format kompatibel).
- **Filter tahun ajaran global** di seluruh modul.
- **Audit log** (siapa mengubah data kritis).

### 2.6 Teknis

- **Backup otomatis** (database + file materi).
- **PWA / push notification** pelengkap WhatsApp.
- **Pencarian global** diperluas (tagihan, jadwal, materi, …).

---

## 3. Prioritas implementasi (usulan urutan)

1. **Fondasi multi-tenant** — model `Sekolah` / `Lembaga`, `sekolah_id`, relasi cabang, middleware scope, migrasi data existing ke satu sekolah default.
2. **Role pengurus cabang** + dashboard & CRUD sekolah + undangan admin operator (email `NPSN@numa.com` atau sesuai keputusan final).
3. **Gateway pembayaran** (nilai bisnis tinggi).
4. **E-Rapor PDF** (kebutuhan akademik kuat).
5. **CBT dasar** (nilai tambah kompetitif).

---

## 4. Catatan hukum & privasi

- Akses pengurus cabang ke data person harus **sesuai kebijakan internal Ma'arif** dan **UU PDP** (tujuan pemrosesan, minimisasi data, retensi).
- Dokumen ini **bukan** spesifikasi hukum; perlu review stakeholder (PC, TU, hukum).

---

## 5. Revisi dokumen

| Tanggal | Perubahan |
|---------|-------------|
| (auto) | Versi awal: rencana fitur + konsep Admin Ma'arif / pengurus cabang + login `NPSN@numa.com`. |

_File: `docs/RENCANA_FITUR_DAN_ADMIN_CABANG.md`_
