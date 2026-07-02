# Panduan Fitur Keuangan — Numa Edu

Dokumen ini ditujukan untuk **operator TU/kasir, admin sekolah, dan pengurus cabang** agar modul keuangan dapat digunakan secara maksimal. Panduan mencakup alur harian, setiap menu, cetak PDF, portal wali, akuntansi, dan pemecahan masalah umum.

**Versi panduan:** sesuai fitur aplikasi per Juli 2026  
**Akses modul:** menu sidebar **Keuangan** atau halaman **Dashboard Keuangan** (`/keuangan`)

---

## Daftar Isi

1. [Ringkasan Sistem](#1-ringkasan-sistem)
2. [Hak Akses per Peran](#2-hak-akses-per-peran)
3. [Konsep & Istilah](#3-konsep--istilah)
4. [Peta Menu Keuangan](#4-peta-menu-keuangan)
5. [Alur Kerja Standar (SOP)](#5-alur-kerja-standar-sop)
6. [Master Kewajiban Pembayaran](#6-master-kewajiban-pembayaran)
7. [Generate Tagihan](#7-generate-tagihan)
8. [Proses Pembayaran (Kasir)](#8-proses-pembayaran-kasir)
9. [Daftar & Detail Tagihan](#9-daftar--detail-tagihan)
10. [Invoice & Kwitansi PDF](#10-invoice--kwitansi-pdf)
11. [Tunggakan](#11-tunggakan)
12. [Rekap Keuangan](#12-rekap-keuangan)
13. [Kas: Buku Kas & Pengeluaran](#13-kas-buku-kas--pengeluaran)
14. [Akuntansi: COA & Jurnal](#14-akuntansi-coa--jurnal)
15. [Pelaporan & Ekspor CSV](#15-pelaporan--ekspor-csv)
16. [Portal Wali Murid](#16-portal-wali-murid)
17. [Otomasi (Scheduler & Command)](#17-otomasi-scheduler--command)
18. [Tips Operasional](#18-tips-operasional)
19. [Pemecahan Masalah (FAQ)](#19-pemecahan-masalah-faq)
20. [Glosarium](#20-glosarium)

---

## 1. Ringkasan Sistem

Modul keuangan Numa Edu dirancang untuk operasional sekolah **Ma'arif NU** dengan alur:

```
Master Kewajiban → Generate Tagihan → Pembayaran (kasir) → Jurnal otomatis → Buku Kas → Rekap & Laporan
```

**Yang sudah tersedia:**
- Setup jenis biaya (SPP, uang gedung, seragam, dll.)
- Tagihan per siswa per periode (`YYYY-MM`)
- Pembayaran manual (tunai, transfer, virtual account, lainnya)
- Pembayaran sebagian (partial) dan multi-tagihan sekaligus
- Jurnal akuntansi double-entry otomatis
- Buku kas, pengeluaran, COA, jurnal manual
- Rekap, tunggakan, ekspor CSV
- **Invoice PDF** (per tagihan) dan **Kwitansi PDF** (per pembayaran)
- **Portal wali:** lihat rincian tagihan & riwayat bayar + unduh PDF

**Yang belum tersedia (saat ini):**
- Pembayaran online / payment gateway (Midtrans, Xendit, dll.)
- Portal siswa untuk keuangan
- Neraca & laporan laba rugi formal
- Notifikasi tagihan otomatis (WhatsApp/email)

---

## 2. Hak Akses per Peran

| Peran | Akses keuangan |
|-------|----------------|
| **Admin sekolah** | Penuh — semua menu keuangan sekolahnya |
| **Pengurus cabang (PC)** | Penuh **setelah memilih sekolah aktif** di menu Sekolah/PC |
| **Super admin** | Penuh — semua sekolah |
| **Guru** | **Tidak** — tidak ada menu Keuangan |
| **Wali murid** | **Terbatas** — lihat tagihan anak + unduh PDF (tidak bisa catat pembayaran) |
| **Siswa** | Tidak ada akses keuangan |

> **Penting untuk pengurus cabang:** Jika menu keuangan kosong atau error, pastikan sudah memilih sekolah lewat **Sekolah / PC** terlebih dahulu.

---

## 3. Konsep & Istilah

### Master Kewajiban Pembayaran
Template biaya sekolah: nama (mis. SPP), tipe, nominal default, batas hari bayar. Digunakan saat **generate tagihan**.

### Tagihan
Invoice internal per **siswa + jenis + periode**. Satu siswa bisa punya banyak tagihan dalam satu bulan (SPP + insidental).

**Status tagihan:**

| Status | Arti |
|--------|------|
| `Belum lunas` (unpaid) | Belum ada pembayaran |
| `Sebagian` (partial) | Sudah dibayar sebagian, masih ada sisa |
| `Lunas` (paid) | Total pembayaran ≥ nominal tagihan |

### Pembayaran
Catatan transaksi bayar terhadap satu tagihan. Satu tagihan bisa punya **banyak pembayaran** (cicilan manual).

### Periode tagihan
Format wajib: **`YYYY-MM`** (contoh: `2026-07` untuk Juli 2026). Filter di kasir dan laporan memakai format ini **secara persis**.

### Jurnal akuntansi
Setiap pembayaran dan pengeluaran kas otomatis membuat jurnal:
- **Pembayaran:** Debit Kas (101) · Kredit Pendapatan SPP (401)
- **Pengeluaran:** Debit Beban (501) · Kredit Kas (101)

### Akun bawaan (auto-create)

| Kode | Nama | Tipe |
|------|------|------|
| 101 | Kas | Aset |
| 401 | Pendapatan SPP | Pendapatan |
| 501 | Beban Operasional | Beban |

---

## 4. Peta Menu Keuangan

Semua menu ada di sidebar **Keuangan**:

| Menu | URL | Fungsi singkat |
|------|-----|----------------|
| Dashboard keuangan | `/keuangan` | Ringkasan piutang, pemasukan bulan ini |
| Daftar tagihan | `/tagihan` | CRUD tagihan, detail & riwayat bayar |
| Proses pembayaran | `/keuangan/proses` | Kasir: generate + bayar multi-tagihan |
| Tunggakan | `/keuangan/tunggakan` | Daftar belum lunas + export CSV |
| Rekap keuangan | `/keuangan/rekap` | Agregat per periode, drill-down siswa/kelas |
| Master kewajiban | `/keuangan/kewajiban` | Setup jenis biaya |
| Buku kas | `/keuangan/buku-kas` | Mutasi akun Kas |
| Pemasukan kas | `/keuangan/pemasukan-kas` | Pemasukan non-siswa (hibah, sewa, dll.) |
| Pengeluaran kas | `/keuangan/pengeluaran-kas` | Catat beban keluar |
| Jurnal & akuntansi | `/akuntansi` | Daftar jurnal |
| Daftar akun (COA) | `/keuangan/coa` | Chart of Accounts |
| Pelaporan | `/laporan` | Ekspor CSV (termasuk keuangan) |

---

## 5. Alur Kerja Standar (SOP)

### SOP Bulanan — Awal Bulan

1. **Pastikan master kewajiban aktif** (`Keuangan → Master kewajiban`)
2. **Generate tagihan bulanan** untuk semua siswa:
   - Via **Proses pembayaran → Generate massal**, atau
   - Otomatis tiap tanggal 1 (lihat [Otomasi](#17-otomasi-scheduler--command))
3. **Cek tunggakan** bulan sebelumnya jika ada siswa belum bayar
4. **Informasikan wali** (manual) — wali bisa cek sendiri di portal **Anak Saya**

### SOP Harian — Saat Siswa/Wali Bayar di Kasir

1. Buka **Proses pembayaran**
2. Pilih **siswa**, **bulan**, dan **tahun** → **Tampilkan**
3. Jika belum ada tagihan → klik **Generate (siswa ini)**
4. Centang tagihan yang dibayar, isi nominal (boleh partial)
5. Pilih **metode bayar**, isi **referensi/no. bukti** (opsional)
6. Klik **Catat pembayaran**
7. Buka **detail tagihan** → unduh **Kwitansi PDF** untuk diserahkan ke wali

### SOP Insidental (Uang Gedung, Study Tour, dll.)

1. Buat master kewajiban tipe **insidental** (jika belum ada)
2. Di **Proses pembayaran → Generate massal → Insidental**, pilih kewajiban & kelas target
3. Lanjut proses bayar seperti SOP harian

### SOP Akhir Bulan

1. **Rekap keuangan** — cek pemasukan per periode
2. **Buku kas** — cocokkan saldo dengan uang fisik di laci/bank
3. **Pelaporan → Unduh CSV** tagihan & pembayaran untuk arsip
4. **Tunggakan** — export daftar siswa belum lunas untuk follow-up

---

## 6. Master Kewajiban Pembayaran

**Menu:** Keuangan → Master kewajiban (`/keuangan/kewajiban`)

### Field formulir

| Field | Keterangan |
|-------|------------|
| **Nama kewajiban** | Contoh: SPP, Uang Gedung, Seragam |
| **Tipe** | `bulanan` — di-generate rutin tiap bulan · `insidental` — sekali bayar / event |
| **Nominal default** | Nominal standar saat generate tagihan |
| **Berlaku mulai** | Format `YYYY-MM` (opsional; disimpan untuk referensi) |
| **Batas bayar (tgl)** | Tanggal jatuh tempo (1–28), mis. `15` = tgl 15 setiap bulan |
| **Status aktif** | Nonaktif = tidak ikut generate otomatis |

### Contoh setup umum

| Nama | Tipe | Nominal | Batas bayar |
|------|------|---------|-------------|
| SPP | bulanan | Rp 150.000 | 15 |
| Uang Gedung | insidental | Rp 500.000 | — |
| Seragam | insidental | Rp 350.000 | — |

### Langkah tambah kewajiban baru

1. Klik **Tambah kewajiban**
2. Isi formulir → **Simpan**
3. Uji dengan generate tagihan 1 siswa dulu sebelum generate massal

---

## 7. Generate Tagihan

Tagihan bisa dibuat dari **3 tempat**:

### A. Proses pembayaran — per siswa
1. Pilih siswa & periode → **Tampilkan**
2. Klik **Generate (siswa ini)**
3. Sistem membuat tagihan dari semua kewajiban **bulanan aktif** yang belum ada untuk siswa+periode tersebut

### B. Proses pembayaran — massal bulanan
1. Klik **Generate massal**
2. Pilih **target kelas** (kosong = semua siswa sekolah)
3. Klik **Generate**
4. Tidak membuat duplikat jika tagihan siswa+periode+jenis sudah ada

### C. Proses pembayaran — massal insidental
1. Di panel **Insidental**, pilih **kewajiban insidental**
2. Pilih kelas target (opsional)
3. Override **nominal** atau **jatuh tempo** jika perlu
4. Klik **Buat tagihan**

### D. Manual — menu Daftar tagihan
1. **Tagihan → Tambah tagihan**
2. Pilih siswa, isi jenis, periode, nominal, jatuh tempo
3. Berguna untuk kasus khusus di luar master kewajiban

### Aturan duplikasi
Sistem **tidak** membuat tagihan baru jika sudah ada kombinasi:
**siswa + periode + jenis** yang sama.

---

## 8. Proses Pembayaran (Kasir)

**Menu:** Keuangan → Proses pembayaran (`/keuangan/proses`)

Halaman ini adalah **pusat operasional kasir** sehari-hari.

### Langkah detail

1. **Pilih siswa** dari dropdown (nama, NIS, kelas ditampilkan)
2. **Pilih bulan & tahun** — sistem membentuk periode `YYYY-MM`
3. Klik **Tampilkan**
4. Tabel menampilkan tagihan **belum lunas** untuk periode tersebut
5. **Centang** tagihan yang akan dibayar (bisa lebih dari satu)
6. Kolom **Bayar**: isi nominal — default = sisa penuh, bisa dikurangi untuk **partial payment**
7. Isi **Metode bayar:**
   - `tunai` — Tunai
   - `transfer` — Transfer bank
   - `virtual` — Virtual account (label manual, bukan integrasi gateway)
   - `lainnya` — Lainnya
8. Isi **Referensi / no. bukti** (nomor transfer, no. kwitansi manual, dll.)
9. **Tanggal bayar** — kosongkan untuk pakai waktu sekarang
10. Klik **Catat pembayaran**

### Setelah pembayaran tercatat
- Status tagihan otomatis diperbarui (lunas / sebagian)
- Jurnal akuntansi otomatis dibuat
- Saldo buku kas bertambah (Debit Kas)

### Generate dari halaman yang sama
Jika siswa belum punya tagihan di periode itu, gunakan:
- **Generate (siswa ini)** — cepat untuk 1 orang
- **Generate massal** — untuk seluruh kelas / sekolah

---

## 9. Daftar & Detail Tagihan

**Menu:** Keuangan → Daftar tagihan (`/tagihan`)

### Daftar tagihan
Filter tersedia: siswa, kelas, status, rentang periode.

### Detail tagihan (`/tagihan/{id}`)
Menampilkan:
- Status, nominal, sudah dibayar, sisa, jatuh tempo
- **Riwayat pembayaran** lengkap
- Form **catat pembayaran** (alternatif selain kasir)
- Tombol **Invoice PDF**
- Link **Kwitansi PDF** per baris pembayaran
- Opsi **hapus pembayaran** (jika salah input — status tagihan dihitung ulang)

### Edit & hapus tagihan
- **Edit:** ubah nominal, periode, jatuh tempo → status dihitung ulang
- **Hapus:** hanya jika memang perlu batalkan tagihan (hati-hati jika sudah ada pembayaran)

---

## 10. Invoice & Kwitansi PDF

Fitur cetak dokumen resmi untuk wali dan arsip sekolah.

### Invoice tagihan
- **URL:** `/tagihan/{id}/invoice.pdf`
- **Tombol:** Detail tagihan → **Invoice PDF**
- **Nomor dokumen:** `INV-{id tagihan}-{periode tanpa strip}`  
  Contoh: `INV-42-202607.pdf`
- **Isi:** kop sekolah, data siswa, rincian tagihan, total/sisa, riwayat pembayaran

### Kwitansi pembayaran
- **URL:** `/pembayaran/{id}/kwitansi.pdf`
- **Tombol:** Detail tagihan → **Kwitansi PDF** (per transaksi)
- **Nomor dokumen:** `KW-{id pembayaran}-{tahun}`  
  Contoh: `KW-15-2026.pdf`
- **Isi:** kop sekolah, penerima/pembayar, uraian, nominal, metode, referensi, ruang tanda tangan

### Kapan memberikan dokumen ke wali?

| Situasi | Dokumen yang diberikan |
|---------|------------------------|
| Siswa bayar di kasir | **Kwitansi PDF** (bukti resmi) |
| Wali minta rincian tagihan | **Invoice PDF** |
| Tagihan belum lunas | Invoice PDF (informasi sisa) |
| Tagihan sudah lunas | Kwitansi PDF tiap pembayaran |

> **Catatan:** Invoice bukan bukti pembayaran resmi. Bukti resmi adalah **Kwitansi PDF** per transaksi.

### Siapa bisa unduh PDF?
- Admin / TU / pengurus cabang (sekolah aktif)
- Wali murid — **hanya untuk anak yang sudah ditautkan** ke akun wali

---

## 11. Tunggakan

**Menu:** Keuangan → Tunggakan (`/keuangan/tunggakan`)

### Fungsi
- Menampilkan semua tagihan **belum lunas** dan **sebagian**
- Filter: periode dari/sampai, kelas
- Kolom: siswa, jenis, periode, tagihan, dibayar, **sisa**
- **Export CSV** untuk follow-up ke wali atau rapat komite

### Kapan digunakan
- Awal bulan — cek siswa yang masih punya tunggakan bulan lalu
- Sebelum naik kelas / kelulusan — pastikan keuangan clear
- Laporan ke pengurus / komite sekolah

---

## 12. Rekap Keuangan

**Menu:** Keuangan → Rekap keuangan (`/keuangan/rekap`)

### Halaman utama
Filter periode → tampil agregat:
- Total tagihan dibuat
- Total terbayar
- Sisa piutang

### Drill-down
- **Per siswa** — klik nama siswa → detail semua tagihan siswa tersebut
- **Per kelas** — klik kelas → ringkasan per siswa di kelas itu

### Kapan digunakan
- Evaluasi bulanan penerimaan sekolah
- Persiapan laporan ke yayasan / PC NU
- Analisis kelas dengan tunggakan tinggi

---

## 13. Kas: Buku Kas, Pemasukan & Pengeluaran

### Buku Kas (`/keuangan/buku-kas`)

Menampilkan **mutasi akun Kas (101)** dari semua jurnal:
- Pemasukan dari pembayaran siswa (Debit Kas)
- Pemasukan non-siswa via **Pemasukan kas** (Debit Kas)
- Pengeluaran operasional (Kredit Kas)

**Filter:** rentang tanggal  
**Ringkasan:** saldo awal periode, saldo akhir periode  
**Export:** Unduh CSV

**Praktik baik:** Setiap akhir minggu/bulan, cocokkan **saldo akhir buku kas** dengan uang tunai di kas sekolah + saldo rekening bank.

### Pemasukan Kas (`/keuangan/pemasukan-kas`)

Modul untuk **uang masuk yang bukan dari siswa**: hibah PC NU, bantuan yayasan, sewa gedung, iuran komite, dll.

**Langkah catat pemasukan:**
1. Keuangan → **Pemasukan kas** → **Catat pemasukan**
2. Isi: tanggal, jumlah, keterangan, no. bukti (opsional)
3. Pilih **akun pendapatan** (default: 401 Pendapatan SPP — disarankan buat akun khusus di COA, mis. 402 Hibah)
4. **Bukti nota / kwitansi (opsional):** unggah PDF atau foto nota fisik (maks. 5 MB)
5. **Simpan**

Sistem otomatis membuat jurnal: **Debit Kas · Kredit Pendapatan**.

**Unduh bukti:** dari kolom **Bukti** di daftar pemasukan.

> **Pembayaran siswa** tetap lewat **Proses pembayaran**, bukan modul ini.

### Pengeluaran Kas (`/keuangan/pengeluaran-kas`)

**Langkah catat pengeluaran:**
1. Klik **Catat pengeluaran**
2. Isi: tanggal, jumlah, keterangan, no. bukti (opsional)
3. Pilih **akun beban** (default: 501 Beban Operasional)
4. **Bukti nota / kwitansi (opsional):** unggah PDF atau foto struk/nota toko (maks. 5 MB)
5. **Simpan**

Sistem otomatis membuat jurnal: **Debit Beban · Kredit Kas**.

**Unduh bukti:** dari kolom **Bukti** di daftar pengeluaran.

**Contoh pengeluaran:** ATK, listrik, honor, konsumsi rapat, perbaikan gedung.

### Upload bukti nota (pemasukan & pengeluaran)

| Item | Ketentuan |
|------|-----------|
| **Wajib?** | Tidak — opsional |
| **Format** | PDF, JPG, JPEG, PNG, WebP |
| **Ukuran maks.** | 5 MB |
| **Penyimpanan** | Server aplikasi (`storage/app/public/keuangan/...`) |
| **Akses** | Admin TU / pengurus cabang (sekolah aktif) |
| **Saat dihapus transaksi** | File bukti ikut dihapus |

**Tips:** Foto nota dengan pencahayaan cukup; untuk nota panjang bisa scan ke PDF.

---

## 14. Akuntansi: COA & Jurnal

### Daftar Akun / COA (`/keuangan/coa`)

Chart of Accounts per sekolah. Tipe akun:
- Aset
- Kewajiban
- Ekuitas
- Pendapatan
- Beban

**Default sudah ada** (101, 401, 501). Tambah akun baru jika sekolah ingin klasifikasi lebih detail (mis. Pendapatan Seragam, Beban Listrik).

> Saat ini semua pembayaran siswa otomatis masuk akun **401 Pendapatan SPP**, terlepas jenis tagihan.

### Jurnal Umum (`/akuntansi`)

**Jenis jurnal:**
| Sumber | Cara entri | Bisa dihapus? |
|--------|------------|---------------|
| Pembayaran | Otomatis | Ya — hapus pembayaran di detail tagihan |
| Pemasukan kas | Otomatis | Ya — hapus di daftar pemasukan kas |
| Pengeluaran kas | Otomatis | Ya — hapus di daftar pengeluaran |
| Manual | Buat lewat **Tambah jurnal** | Ya — hapus di daftar jurnal |

**Jurnal manual** — untuk transaksi khusus di luar modul di atas (mis. setoran modal, penyesuaian akuntansi).

**Validasi:** total debit harus = total kredit.

**Export:** jurnal bisa diunduh CSV dari halaman jurnal.

---

## 15. Pelaporan & Ekspor CSV

**Menu:** Pelaporan (`/laporan`)

### Ekspor keuangan

| File | Isi |
|------|-----|
| **Tagihan (CSV)** | Semua tagihan dengan filter periode/kelas/status |
| **Pembayaran (CSV)** | Riwayat pembayaran dengan filter |
| **Tunggakan (CSV)** | Dari halaman tunggakan |

### Filter di halaman pelaporan
- Periode dari / sampai (`YYYY-MM`)
- Kelas (opsional)
- Status tagihan
- Metode bayar

### Cara pakai
1. Atur filter → **Terapkan**
2. Klik tombol unduh CSV yang diinginkan
3. Buka di Excel / Google Sheets untuk analisis lanjutan

---

## 16. Portal Wali Murid

Wali dapat **memantau tagihan anak** tanpa perlu datang ke sekolah hanya untuk cek status.

### Prasyarat wali
1. Wali punya akun dengan peran **wali**
2. Akun sudah **ditautkan ke siswa** (via onboarding NPSN+NIS atau oleh admin di **Admin Wali**)
3. Wali login → menu **Anak Saya** (`/wali`)

### Navigasi wali

```
Anak Saya → Pilih siswa → Kartu "Tagihan belum lunas" → Tagihan & pembayaran
```

**URL langsung:** `/wali/{siswa}/tagihan`

### Fitur portal wali

| Fitur | Keterangan |
|-------|------------|
| Statistik | Total tagihan, belum lunas, sisa, total dibayar |
| Daftar tagihan | Per periode & jenis, dengan status |
| Filter status | Lunas / sebagian / belum lunas |
| Detail tagihan | Rincian + riwayat bayar per tagihan |
| Riwayat pembayaran | 50 transaksi terakhir |
| Unduh PDF | Invoice & kwitansi (read-only) |

### Batasan portal wali
- **Tidak bisa** mencatat pembayaran — pembayaran tetap via kasir sekolah
- **Tidak bisa** melihat data siswa sekolah lain
- **Tidak bisa** edit/hapus tagihan atau pembayaran

### Panduan untuk operator: aktivasi wali

1. **Admin → Admin Wali** (`/admin/wali`)
2. Buat akun wali atau tautkan siswa ke akun existing
3. Informasikan email & password ke wali
4. Wali login → selesaikan **hubungkan akun** jika diminta (NPSN + NIS + tanggal lahir)
5. Wali bisa cek tagihan kapan saja

---

## 17. Otomasi (Scheduler & Command)

### Generate otomatis tiap tanggal 1
Sistem menjalankan perintah `keuangan:generate-bulanan` **setiap tanggal 1 pukul 00:10** untuk periode bulan berjalan.

**Syarat server:** cron harus menjalankan:
```bash
* * * * * cd /path/to/numa-edu && php artisan schedule:run >> /dev/null 2>&1
```

**Catatan penting:** Scheduler default hanya untuk **sekolah default** (`tenancy.default_sekolah_id`). Untuk multi-sekolah, operator perlu generate manual per sekolah atau minta tim IT menjalankan command per sekolah.

### Command manual (tim IT / server)

```bash
# Generate tagihan bulan Juli 2026 untuk sekolah ID 1
php artisan keuangan:generate-bulanan 2026-07 --sekolah_id=1

# Batasi ke 1 kelas
php artisan keuangan:generate-bulanan 2026-07 --sekolah_id=1 --kelas_id=5
```

### Audit periode tagihan
```bash
php artisan tagihan:periode-audit
```
Digunakan untuk maintenance/normalisasi format periode tagihan lama.

---

## 18. Tips Operasional

### ✅ Praktik terbaik

1. **Setup master kewajiban dulu** sebelum tahun ajaran baru
2. **Generate tagihan awal bulan** — jangan menunggu siswa datang bayar
3. **Selalu isi referensi/no. bukti** saat transfer — memudahkan audit
4. **Berikan kwitansi PDF** setiap transaksi ke wali
5. **Cek buku kas** rutin — cocokkan dengan fisik
6. **Export CSV** tiap akhir bulan untuk arsip
7. **Gunakan periode konsisten** `YYYY-MM` — hindari format lain seperti `2025/2026`
8. **Aktifkan akun wali** agar wali bisa cek mandiri → mengurangi antrian tanya status

### ⚠️ Hal yang perlu dihindari

1. Menghapus pembayaran tanpa alasan jelas — jurnal ikut terhapus
2. Membuat tagihan duplikat manual — gunakan generate
3. Mencatat pembayaran tanpa centang tagihan yang benar di kasir
4. Mengabaikan tagihan **sebagian** — follow-up sisa sebelum akhir periode

### Partial payment (cicilan manual)
Sistem mendukung bayar sebagian. Contoh tagihan SPP Rp 150.000:
- Bayar Rp 75.000 → status **Sebagian**, sisa Rp 75.000
- Bayar Rp 75.000 lagi → status **Lunas**
- Setiap pembayaran punya **kwitansi PDF sendiri**

---

## 19. Pemecahan Masalah (FAQ)

### Tagihan tidak muncul di Proses Pembayaran
**Penyebab umum:**
1. Periode filter tidak cocok — pastikan format `YYYY-MM` sama dengan kolom periode tagihan
2. Tagihan sudah lunas — halaman kasir hanya tampilkan yang belum lunas
3. Tagihan belum di-generate — klik **Generate (siswa ini)**

**Solusi:** Cek di **Daftar tagihan** dengan filter siswa & periode yang sama.

### Generate massal tidak membuat tagihan baru
Tagihan untuk kombinasi siswa+periode+jenis **sudah ada**. Cek di daftar tagihan.

### Wali tidak bisa lihat tagihan
1. Pastikan siswa sudah **ditautkan** ke akun wali
2. Wali harus login dengan akun yang benar
3. Wali harus selesai **onboarding hubungkan akun** jika baru daftar

### PDF tidak terunduh / error
1. Pastikan package DomPDF terinstall di server (`barryvdh/laravel-dompdf`)
2. Cek profil sekolah sudah diisi (nama sekolah muncul di kop PDF)
3. Pastikan user punya hak akses view tagihan/pembayaran

### Saldo buku kas tidak cocok dengan uang fisik
1. Cek apakah semua pembayaran tercatat
2. Cek apakah pengeluaran kas sudah dicatat
3. Cek jurnal manual yang mungkin belum diinput
4. Ingat: buku kas hanya akun **101 Kas** — transfer langsung ke rekening bank tanpa dicatat tidak akan muncul

### Pengurus cabang tidak bisa akses keuangan
Pilih sekolah aktif dulu di menu **Sekolah / PC**.

### Scheduler generate tidak jalan
1. Pastikan cron `schedule:run` aktif di server
2. Default hanya sekolah ID 1 — multi-sekolah perlu command manual
3. Cek log Laravel untuk error

### Status tagihan tidak berubah setelah bayar
Refresh halaman. Jika masih salah, cek di detail tagihan apakah pembayaran tercatat. Hubungi admin jika nominal bayar melebihi sisa (validasi seharusnya mencegah ini).

---

## 20. Glosarium

| Istilah | Definisi |
|---------|----------|
| **Tagihan** | Invoice/kewajiban finansial per siswa per periode |
| **Kewajiban** | Master/template jenis biaya sekolah |
| **Pembayaran** | Transaksi penerimaan uang terhadap tagihan |
| **Partial payment** | Pembayaran sebagian — status tagihan jadi "Sebagian" |
| **Periode** | Bulan tagihan format `YYYY-MM` |
| **COA** | Chart of Accounts — daftar akun akuntansi |
| **Jurnal** | Catatan double-entry debit/kredit |
| **Buku kas** | Laporan mutasi akun Kas |
| **Piutang / tunggakan** | Tagihan yang belum dibayar penuh |
| **Invoice PDF** | Rincian tagihan (bukan bukti bayar) |
| **Kwitansi PDF** | Bukti resmi per transaksi pembayaran |
| **Tenant / sekolah_id** | Isolasi data per sekolah dalam sistem multi-sekolah |

---

## Lampiran: Diagram Alur Lengkap

```
┌─────────────────────┐
│ Master Kewajiban    │  SPP, Uang Gedung, dll.
│ (bulanan/insidental)│
└──────────┬──────────┘
           │ generate
           ▼
┌─────────────────────┐
│ Tagihan per siswa   │  status: unpaid / partial / paid
│ periode YYYY-MM     │
└──────────┬──────────┘
           │ bayar (kasir / detail tagihan)
           ▼
┌─────────────────────┐     ┌──────────────────┐
│ Pembayaran          │────▶│ Kwitansi PDF     │ → serahkan ke wali
└──────────┬──────────┘     └──────────────────┘
           │ otomatis
           ▼
┌─────────────────────┐
│ Jurnal: Debit Kas   │
│         Kredit 401  │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐     ┌──────────────────┐
│ Buku Kas (akun 101) │     │ Invoice PDF      │ → informasi tagihan
└─────────────────────┘     └──────────────────┘
           ▲
           │ pengeluaran kas
┌─────────────────────┐
│ Jurnal: Debit Beban │
│         Kredit Kas  │
└─────────────────────┘
```

---

## Kontak & Dukungan

Untuk kendala teknis (error sistem, scheduler, multi-sekolah), hubungi **administrator IT / pengembang** Numa Edu.

Untuk kendala operasional (cara input, interpretasi laporan), gunakan panduan ini atau konsultasikan dengan **admin sekolah / pengurus cabang**.

---

*Dokumen ini akan diperbarui seiring penambahan fitur (payment gateway, notifikasi, neraca, dll.).*
