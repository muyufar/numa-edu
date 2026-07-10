<?php

use App\Http\Controllers\AkunOnboardingController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\KenaikanKelasController;
use App\Http\Controllers\AkuntansiAkunController;
use App\Http\Controllers\AkuntansiController;
use App\Http\Controllers\AkuntansiJurnalController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\BeritaPublicController;
use App\Http\Controllers\BkDashboardController;
use App\Http\Controllers\BkHomeVisitController;
use App\Http\Controllers\BkJenisPelanggaranController;
use App\Http\Controllers\BkPemanggilanController;
use App\Http\Controllers\BkSanksiController;
use App\Http\Controllers\BukuKasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeployRunnerController;
use App\Http\Controllers\EkstrakurikulerController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\InventarisBarangController;
use App\Http\Controllers\InventarisKategoriController;
use App\Http\Controllers\InventarisMutasiController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KeuanganHubController;
use App\Http\Controllers\KeuanganPdfController;
use App\Http\Controllers\KeuanganRekapController;
use App\Http\Controllers\KeuanganTunggakanController;
use App\Http\Controllers\KewajibanPembayaranController;
use App\Http\Controllers\KinerjaPenilaianController;
use App\Http\Controllers\KokurikulerKegiatanController;
use App\Http\Controllers\KurikulumItemController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LembagaRegistrationNpsnLookupController;
use App\Http\Controllers\LombaAjangController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\MateriAjarController;
use App\Http\Controllers\TenagaKependidikanController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\MouLembagaPublicController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\NominatimProxyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemasukanKasController;
use App\Http\Controllers\PendaftaranLembagaPublicController;
use App\Http\Controllers\PendaftaranPpdbController;
use App\Http\Controllers\PengeluaranKasController;
use App\Http\Controllers\PengurusCabang\LembagaMouCabangSettingsController;
use App\Http\Controllers\PengurusCabang\LembagaRegistrationController;
use App\Http\Controllers\PengurusCabang\SekolahPendaftaranController;
use App\Http\Controllers\PengurusCabang\SekolahPilihController;
use App\Http\Controllers\PengurusCabang\SekolahProfilController;
use App\Http\Controllers\PerpustakaanBukuController;
use App\Http\Controllers\PerpustakaanDashboardController;
use App\Http\Controllers\PerpustakaanKategoriController;
use App\Http\Controllers\PerpustakaanPeminjamanController;
use App\Http\Controllers\PerpustakaanPengaturanController;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\PpdbRegistrationController;
use App\Http\Controllers\PresensiGuruController;
use App\Http\Controllers\PresensiHubController;
use App\Http\Controllers\PresensiPegawaiController;
use App\Http\Controllers\PresensiPengaturanController;
use App\Http\Controllers\PresensiScanController;
use App\Http\Controllers\PresensiSiswaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RewardSiswaController;
use App\Http\Controllers\ProsesPembayaranController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SekolahLembagaProfilController;
use App\Http\Controllers\SiswaAkunAdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\WaliAdminController;
use App\Http\Controllers\WaliHubController;
use App\Http\Controllers\WaliKeuanganController;
use App\Http\Controllers\WaliSiswaController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\WilayahProxyController;
use Illuminate\Support\Facades\Route;

Route::prefix('ref/wilayah')->middleware('throttle:120,1')->group(function () {
    Route::get('provinces', [WilayahProxyController::class, 'provinces'])->name('ref.wilayah.provinces');
    Route::get('regencies/{kode}', [WilayahProxyController::class, 'regencies'])->where('kode', '[0-9.]+')->name('ref.wilayah.regencies');
    Route::get('districts/{kode}', [WilayahProxyController::class, 'districts'])->where('kode', '[0-9.]+')->name('ref.wilayah.districts');
    Route::get('villages/{kode}', [WilayahProxyController::class, 'villages'])->where('kode', '[0-9.]+')->name('ref.wilayah.villages');
});

Route::prefix('ref/nominatim')->middleware('throttle:30,1')->group(function () {
    Route::get('reverse', [NominatimProxyController::class, 'reverse'])->name('ref.nominatim.reverse');
    Route::get('search', [NominatimProxyController::class, 'search'])->name('ref.nominatim.search');
});

Route::get('/', WelcomeController::class)->name('welcome');

Route::get('ppdb/daftar', [PendaftaranPpdbController::class, 'create'])->name('ppdb.daftar');
Route::post('ppdb/daftar', [PendaftaranPpdbController::class, 'store'])->name('ppdb.daftar.store');

Route::middleware('throttle:40,1')->group(function () {
    Route::get('pendaftaran-lembaga', [PendaftaranLembagaPublicController::class, 'create'])->name('public.lembaga-registrations.create');
    Route::get('pendaftaran-lembaga/cek-status', [LembagaRegistrationNpsnLookupController::class, 'create'])->name('public.lembaga-registrations.check-status');
    Route::post('pendaftaran-lembaga/cek-status', [LembagaRegistrationNpsnLookupController::class, 'store'])->name('public.lembaga-registrations.check-status.submit');
    Route::post('pendaftaran-lembaga', [PendaftaranLembagaPublicController::class, 'store'])->name('public.lembaga-registrations.store');

    Route::get('l/{token}/ubah', [PendaftaranLembagaPublicController::class, 'edit'])
        ->whereUuid('token')
        ->name('public.lembaga-registrations.edit');
    Route::put('l/{token}', [PendaftaranLembagaPublicController::class, 'update'])
        ->whereUuid('token')
        ->name('public.lembaga-registrations.update');
    Route::get('l/{token}/mou', [MouLembagaPublicController::class, 'show'])
        ->whereUuid('token')
        ->name('public.lembaga-registrations.mou');
    Route::post('l/{token}/mou', [MouLembagaPublicController::class, 'sign'])
        ->whereUuid('token')
        ->name('public.lembaga-registrations.mou.sign');
    Route::get('l/{token}/status', [MouLembagaPublicController::class, 'status'])
        ->whereUuid('token')
        ->name('public.lembaga-registrations.status');
    Route::post('l/{token}/pdf-ulang', [MouLembagaPublicController::class, 'regeneratePdfs'])
        ->middleware('throttle:12,1')
        ->whereUuid('token')
        ->name('public.lembaga-registrations.pdf-regenerate');

    Route::get('pendaftaran-lembaga/{token}/ubah', function (string $token) {
        return redirect()->route('public.lembaga-registrations.edit', ['token' => $token], 301);
    })->whereUuid('token');
    Route::get('pendaftaran-lembaga/{token}/mou', function (string $token) {
        return redirect()->route('public.lembaga-registrations.mou', ['token' => $token], 301);
    })->whereUuid('token');
    Route::get('pendaftaran-lembaga/{token}/status', function (string $token) {
        return redirect()->route('public.lembaga-registrations.status', ['token' => $token], 301);
    })->whereUuid('token');

    Route::post('pendaftaran-lembaga/{token}/mou', [MouLembagaPublicController::class, 'sign'])
        ->whereUuid('token');
    Route::put('pendaftaran-lembaga/{token}', [PendaftaranLembagaPublicController::class, 'update'])
        ->whereUuid('token');
});

Route::get('/informasi', [BeritaPublicController::class, 'index'])->name('informasi.index');
Route::get('/informasi/{slug}', [BeritaPublicController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('informasi.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'throttle:30,1'])->group(function () {
    Route::get('onboarding/hubungkan', [AkunOnboardingController::class, 'show'])->name('onboarding.hubungkan');
    Route::post('onboarding/hubungkan', [AkunOnboardingController::class, 'store'])->name('onboarding.hubungkan.store');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('profil-lembaga/edit', [SekolahLembagaProfilController::class, 'edit'])->name('profil-lembaga.edit');
    Route::put('profil-lembaga', [SekolahLembagaProfilController::class, 'update'])->name('profil-lembaga.update');
});

Route::middleware(['auth', 'verified'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('sekolah', [SekolahPilihController::class, 'index'])->name('sekolah.index');
    Route::get('sekolah/baru', [SekolahPendaftaranController::class, 'create'])->name('sekolah.create');
    Route::post('sekolah', [SekolahPendaftaranController::class, 'store'])->name('sekolah.store');
    Route::get('sekolah/{sekolah}/edit', [SekolahProfilController::class, 'edit'])->name('sekolah.edit');
    Route::put('sekolah/{sekolah}', [SekolahProfilController::class, 'update'])->name('sekolah.update');
    Route::post('sekolah/pilih', [SekolahPilihController::class, 'pilih'])->name('sekolah.pilih');
    Route::post('sekolah/reset', [SekolahPilihController::class, 'reset'])->name('sekolah.reset');

    Route::middleware('role:super_admin|pengurus_cabang')->group(function () {
        Route::get('lembaga-mou-settings', [LembagaMouCabangSettingsController::class, 'edit'])->name('lembaga-mou-settings.edit');
        Route::put('lembaga-mou-settings', [LembagaMouCabangSettingsController::class, 'update'])->name('lembaga-mou-settings.update');
        Route::get('lembaga-registrations', [LembagaRegistrationController::class, 'index'])->name('lembaga-registrations.index');
        Route::get('lembaga-registrations/{lembaga_registration}', [LembagaRegistrationController::class, 'show'])->name('lembaga-registrations.show');
        Route::post('lembaga-registrations/{lembaga_registration}/approve', [LembagaRegistrationController::class, 'approve'])->name('lembaga-registrations.approve');
        Route::post('lembaga-registrations/{lembaga_registration}/reject', [LembagaRegistrationController::class, 'reject'])->name('lembaga-registrations.reject');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('tagihan/{tagihan}/invoice.pdf', [KeuanganPdfController::class, 'tagihanInvoice'])->name('tagihan.invoice.pdf');
    Route::get('pembayaran/{pembayaran}/kwitansi.pdf', [KeuanganPdfController::class, 'kwitansiPembayaran'])->name('pembayaran.kwitansi.pdf');

    Route::middleware('role:super_admin|admin|pengurus_cabang')->group(function () {
        Route::get('pengaturan/presensi', [PresensiPengaturanController::class, 'edit'])->name('pengaturan.presensi.edit');
        Route::put('pengaturan/presensi', [PresensiPengaturanController::class, 'update'])->name('pengaturan.presensi.update');

        Route::get('akuntansi', [AkuntansiController::class, 'index'])->name('akuntansi.index');
        Route::get('akuntansi/jurnal', [AkuntansiJurnalController::class, 'index'])->name('akuntansi.jurnal.index');
        Route::get('akuntansi/jurnal/export', [AkuntansiJurnalController::class, 'exportCsv'])->name('akuntansi.jurnal.export');
        Route::get('akuntansi/jurnal/create', [AkuntansiJurnalController::class, 'create'])->name('akuntansi.jurnal.create');
        Route::post('akuntansi/jurnal', [AkuntansiJurnalController::class, 'store'])->name('akuntansi.jurnal.store');
        Route::get('akuntansi/jurnal/{jurnal}', [AkuntansiJurnalController::class, 'show'])->name('akuntansi.jurnal.show');
        Route::delete('akuntansi/jurnal/{jurnal}', [AkuntansiJurnalController::class, 'destroy'])->name('akuntansi.jurnal.destroy');
        Route::get('keuangan/kewajiban', [KewajibanPembayaranController::class, 'index'])->name('keuangan.kewajiban.index');
        Route::get('keuangan/kewajiban/create', [KewajibanPembayaranController::class, 'create'])->name('keuangan.kewajiban.create');
        Route::post('keuangan/kewajiban', [KewajibanPembayaranController::class, 'store'])->name('keuangan.kewajiban.store');
        Route::get('keuangan/kewajiban/{kewajiban}/edit', [KewajibanPembayaranController::class, 'edit'])->name('keuangan.kewajiban.edit');
        Route::put('keuangan/kewajiban/{kewajiban}', [KewajibanPembayaranController::class, 'update'])->name('keuangan.kewajiban.update');
        Route::delete('keuangan/kewajiban/{kewajiban}', [KewajibanPembayaranController::class, 'destroy'])->name('keuangan.kewajiban.destroy');

        Route::get('keuangan/proses', [ProsesPembayaranController::class, 'index'])->name('keuangan.proses.index');
        Route::post('keuangan/proses/generate', [ProsesPembayaranController::class, 'generate'])->name('keuangan.proses.generate');
        Route::post('keuangan/proses/generate-mass', [ProsesPembayaranController::class, 'generateMass'])->name('keuangan.proses.generate-mass');
        Route::post('keuangan/proses/generate-insidental', [ProsesPembayaranController::class, 'generateInsidentalMass'])->name('keuangan.proses.generate-insidental');
        Route::post('keuangan/proses/bayar', [ProsesPembayaranController::class, 'bayar'])->name('keuangan.proses.bayar');

        Route::get('admin/wali', [WaliAdminController::class, 'index'])->name('wali-admin.index');
        Route::get('admin/wali/create', [WaliAdminController::class, 'create'])->name('wali-admin.create');
        Route::post('admin/wali', [WaliAdminController::class, 'store'])->name('wali-admin.store');
        Route::get('admin/wali/{user}', [WaliAdminController::class, 'show'])->name('wali-admin.show');
        Route::put('admin/wali/{user}', [WaliAdminController::class, 'update'])->name('wali-admin.update');
        Route::post('admin/wali/{user}/reset-password', [WaliAdminController::class, 'resetPassword'])->name('wali-admin.reset-password');
        Route::post('admin/wali/{user}/tautkan-anak', [WaliAdminController::class, 'attachSiswa'])->name('wali-admin.attach-siswa');

        Route::get('admin/akun-siswa', [SiswaAkunAdminController::class, 'index'])->name('siswa-akun-admin.index');

        Route::get('keuangan', [KeuanganHubController::class, 'index'])->name('keuangan.index');
        Route::get('keuangan/rekap', [KeuanganRekapController::class, 'index'])->name('keuangan.rekap.index');
        Route::get('keuangan/rekap/siswa/{siswa}', [KeuanganRekapController::class, 'showSiswa'])->name('keuangan.rekap.siswa');
        Route::get('keuangan/rekap/kelas/{kelas}', [KeuanganRekapController::class, 'showKelas'])->name('keuangan.rekap.kelas');

        Route::get('keuangan/tunggakan', [KeuanganTunggakanController::class, 'index'])->name('keuangan.tunggakan.index');
        Route::get('keuangan/tunggakan/export', [KeuanganTunggakanController::class, 'exportCsv'])->name('keuangan.tunggakan.export');

        Route::get('keuangan/pengeluaran-kas', [PengeluaranKasController::class, 'index'])->name('keuangan.pengeluaran-kas.index');
        Route::get('keuangan/pengeluaran-kas/create', [PengeluaranKasController::class, 'create'])->name('keuangan.pengeluaran-kas.create');
        Route::post('keuangan/pengeluaran-kas', [PengeluaranKasController::class, 'store'])->name('keuangan.pengeluaran-kas.store');
        Route::get('keuangan/pengeluaran-kas/{pengeluaran_kas}/bukti-nota', [PengeluaranKasController::class, 'buktiNota'])->name('keuangan.pengeluaran-kas.bukti-nota');
        Route::delete('keuangan/pengeluaran-kas/{pengeluaran_kas}', [PengeluaranKasController::class, 'destroy'])->name('keuangan.pengeluaran-kas.destroy');

        Route::get('keuangan/pemasukan-kas', [PemasukanKasController::class, 'index'])->name('keuangan.pemasukan-kas.index');
        Route::get('keuangan/pemasukan-kas/create', [PemasukanKasController::class, 'create'])->name('keuangan.pemasukan-kas.create');
        Route::post('keuangan/pemasukan-kas', [PemasukanKasController::class, 'store'])->name('keuangan.pemasukan-kas.store');
        Route::get('keuangan/pemasukan-kas/{pemasukan_kas}/bukti-nota', [PemasukanKasController::class, 'buktiNota'])->name('keuangan.pemasukan-kas.bukti-nota');
        Route::delete('keuangan/pemasukan-kas/{pemasukan_kas}', [PemasukanKasController::class, 'destroy'])->name('keuangan.pemasukan-kas.destroy');

        Route::get('keuangan/buku-kas', [BukuKasController::class, 'index'])->name('keuangan.buku-kas.index');
        Route::get('keuangan/buku-kas/export', [BukuKasController::class, 'exportCsv'])->name('keuangan.buku-kas.export');

        Route::resource('keuangan/coa', AkuntansiAkunController::class)
            ->except(['show'])
            ->names('keuangan.coa');

        Route::resource('tagihan', TagihanController::class);
        Route::post('tagihan/{tagihan}/pembayaran', [PembayaranController::class, 'store'])->name('tagihan.pembayaran.store');
        Route::delete('pembayaran/{pembayaran}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');

        Route::resource('berita', BeritaController::class)->except(['show']);
    });

    // Materi/Bahan ajar: semua user login bisa lihat sesuai policy
    Route::get('materi/{materi_ajar}/download', [MateriAjarController::class, 'download'])->name('materi.download');
    Route::get('materi/{materi_ajar}/preview', [MateriAjarController::class, 'preview'])->name('materi.preview');
    Route::post('materi/{materi_ajar}/publikasi', [MateriAjarController::class, 'publish'])->name('materi.publish');
    Route::post('materi/{materi_ajar}/arsipkan', [MateriAjarController::class, 'archive'])->name('materi.archive');
    Route::patch('materi/{materi_ajar}/penggunaan', [MateriAjarController::class, 'updatePenggunaan'])->name('materi.penggunaan');
    Route::resource('materi', MateriAjarController::class)->parameters(['materi' => 'materi_ajar']);

    // Tugas: semua user login bisa lihat sesuai policy
    Route::get('tugas/{tugas}/download', [TugasController::class, 'download'])->name('tugas.download');
    Route::get('tugas/{tugas}/kerjakan', [TugasController::class, 'kerjakan'])->name('tugas.kerjakan');
    Route::post('tugas/{tugas}/kerjakan', [TugasController::class, 'submitKerjakan'])->name('tugas.kerjakan.store');
    Route::resource('tugas', TugasController::class)->parameters(['tugas' => 'tugas']);

    Route::prefix('perpustakaan')->name('perpustakaan.')->group(function () {
        Route::get('/', [PerpustakaanDashboardController::class, 'index'])->name('dashboard');
        Route::get('buku/{perpustakaan_buku}/preview', [PerpustakaanBukuController::class, 'preview'])->name('buku.preview');
        Route::get('buku/{perpustakaan_buku}/cover', [PerpustakaanBukuController::class, 'cover'])->name('buku.cover');
        Route::post('buku/{perpustakaan_buku}/pinjam', [PerpustakaanBukuController::class, 'pinjam'])->name('buku.pinjam');
        Route::resource('buku', PerpustakaanBukuController::class)->parameters(['buku' => 'perpustakaan_buku']);
        Route::resource('kategori', PerpustakaanKategoriController::class)->except(['show'])->parameters(['kategori' => 'perpustakaan_kategori']);
        Route::post('peminjaman/{perpustakaan_peminjaman}/kembalikan', [PerpustakaanPeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
        Route::post('peminjaman/{perpustakaan_peminjaman}/perpanjang', [PerpustakaanPeminjamanController::class, 'perpanjang'])->name('peminjaman.perpanjang');
        Route::post('peminjaman/{perpustakaan_peminjaman}/hilang', [PerpustakaanPeminjamanController::class, 'tandaiHilang'])->name('peminjaman.hilang');
        Route::resource('peminjaman', PerpustakaanPeminjamanController::class)->only(['index', 'show'])->parameters(['peminjaman' => 'perpustakaan_peminjaman']);
        Route::get('pengaturan', [PerpustakaanPengaturanController::class, 'edit'])->name('pengaturan.edit');
        Route::put('pengaturan', [PerpustakaanPengaturanController::class, 'update'])->name('pengaturan.update');
    });

    Route::get('presensi/siswa', [PresensiSiswaController::class, 'index'])->name('presensi.siswa.index');

    Route::middleware('role:super_admin|admin|guru|pengurus_cabang')->group(function () {
        Route::post('kelas/{kelas}/siswa', [KelasController::class, 'attachSiswa'])->name('kelas.siswa.attach');
        Route::resource('kelas', KelasController::class)
            ->except(['show'])
            ->parameters(['kelas' => 'kelas']);
        Route::resource('mapel', MataPelajaranController::class)->except(['show'])->parameters(['mapel' => 'mataPelajaran']);
        Route::resource('kurikulum', KurikulumItemController::class)
            ->except(['show'])
            ->parameters(['kurikulum' => 'kurikulum_item']);
        Route::delete('siswa/bulk', [SiswaController::class, 'destroyBulk'])->name('siswa.destroy-bulk');
        Route::delete('siswa', [SiswaController::class, 'destroyAll'])->name('siswa.destroy-all');
        Route::get('siswa/alumni', [AlumniController::class, 'index'])->name('siswa.alumni.index');
        Route::get('siswa/kenaikan-kelas', [KenaikanKelasController::class, 'index'])->name('siswa.kenaikan-kelas.index');
        Route::post('siswa/kenaikan-kelas/naik', [KenaikanKelasController::class, 'promote'])->name('siswa.kenaikan-kelas.naik');
        Route::post('siswa/kenaikan-kelas/luluskan', [KenaikanKelasController::class, 'graduate'])->name('siswa.kenaikan-kelas.luluskan');
        Route::resource('siswa', SiswaController::class)->except(['show']);
        Route::put('siswa/{siswa}/dokumen', [SiswaController::class, 'updateDokumen'])->name('siswa.dokumen.update');
        Route::post('siswa/{siswa}/buat-akun', [SiswaController::class, 'buatAkun'])->name('siswa.buat-akun');
        Route::put('siswa/{siswa}/akun', [SiswaController::class, 'updateAkun'])->name('siswa.akun.update');
        Route::post('siswa/{siswa}/akun/reset-password', [SiswaController::class, 'resetPasswordAkun'])->name('siswa.akun.reset-password');
        Route::get('siswa/{siswa}/wali', [WaliSiswaController::class, 'edit'])->name('siswa.wali.edit');
        Route::post('siswa/{siswa}/wali', [WaliSiswaController::class, 'store'])->name('siswa.wali.store');
        Route::post('siswa/{siswa}/wali/buat-akun', [WaliSiswaController::class, 'buatAkunWali'])->name('siswa.wali.buat-akun');
        Route::delete('siswa/{siswa}/wali/{user}', [WaliSiswaController::class, 'destroy'])->name('siswa.wali.destroy');
        Route::get('tenaga-kependidikan', [TenagaKependidikanController::class, 'index'])->name('tenaga-kependidikan.index');
        Route::post('tenaga-kependidikan/import', [TenagaKependidikanController::class, 'importStore'])->name('tenaga-kependidikan.import');
        Route::get('guru', fn (\Illuminate\Http\Request $request) => redirect()->route('tenaga-kependidikan.index', array_merge($request->query(), ['tab' => 'guru'])))->name('guru.index');
        Route::get('pegawai', fn (\Illuminate\Http\Request $request) => redirect()->route('tenaga-kependidikan.index', array_merge($request->query(), ['tab' => 'pegawai'])))->name('pegawai.index');
        Route::resource('guru', GuruController::class)->except(['index']);
        Route::resource('pegawai', PegawaiController::class)->except(['index']);
        Route::resource('jadwal', JadwalController::class)->except(['show']);

        Route::get('nilai/bulk', [NilaiController::class, 'bulkCreate'])->name('nilai.bulk.create');
        Route::post('nilai/bulk', [NilaiController::class, 'bulkStore'])->name('nilai.bulk.store');
        Route::resource('nilai', NilaiController::class)->except(['show']);

        Route::get('bk', [BkDashboardController::class, 'index'])->name('bk.dashboard');
        Route::resource('bk/jenis-pelanggaran', BkJenisPelanggaranController::class)
            ->except(['show'])
            ->parameters(['jenis-pelanggaran' => 'bk_jenis_pelanggaran'])
            ->names('bk.jenis-pelanggaran');
        Route::resource('bk/sanksi', BkSanksiController::class)
            ->except(['show'])
            ->parameters(['sanksi' => 'bk_sanksi'])
            ->names('bk.sanksi');
        Route::resource('bk/pelanggaran', PelanggaranController::class)->except(['show'])->names('bk.pelanggaran');
        Route::resource('bk/pemanggilan', BkPemanggilanController::class)
            ->except(['show'])
            ->parameters(['pemanggilan' => 'bk_pemanggilan'])
            ->names('bk.pemanggilan');
        Route::resource('bk/home-visit', BkHomeVisitController::class)
            ->except(['show'])
            ->parameters(['home-visit' => 'bk_home_visit'])
            ->names('bk.home-visit');
        Route::post('bk/home-visit/{bk_home_visit}/lapor-kepsek', [BkHomeVisitController::class, 'laporKepsek'])
            ->name('bk.home-visit.lapor-kepsek');

        Route::resource('kesiswaan/reward', RewardSiswaController::class)
            ->except(['show'])
            ->parameters(['reward' => 'reward_siswa'])
            ->names('kesiswaan.reward');
        Route::resource('kesiswaan/lomba', LombaAjangController::class)
            ->except(['show'])
            ->parameters(['lomba' => 'lomba_ajang'])
            ->names('kesiswaan.lomba');
        Route::resource('kesiswaan/ekstrakurikuler', EkstrakurikulerController::class)
            ->except(['show'])
            ->parameters(['ekstrakurikuler' => 'ekstrakurikuler'])
            ->names('kesiswaan.ekstrakurikuler');
        Route::post('kesiswaan/ekstrakurikuler/{ekstrakurikuler}/kegiatan', [EkstrakurikulerController::class, 'storeKegiatan'])
            ->name('kesiswaan.ekstrakurikuler.kegiatan.store');
        Route::delete('kesiswaan/ekstrakurikuler/{ekstrakurikuler}/kegiatan/{ekstrakurikuler_kegiatan}', [EkstrakurikulerController::class, 'destroyKegiatan'])
            ->name('kesiswaan.ekstrakurikuler.kegiatan.destroy');
        Route::resource('kesiswaan/kokurikuler', KokurikulerKegiatanController::class)
            ->except(['show'])
            ->parameters(['kokurikuler' => 'kokurikuler_kegiatan'])
            ->names('kesiswaan.kokurikuler');

        Route::resource('perizinan', PerizinanController::class)->except(['show']);
        Route::resource('kinerja', KinerjaPenilaianController::class)
            ->except(['show'])
            ->parameters(['kinerja' => 'kinerja_penilaian']);
        Route::prefix('inventaris')->name('inventaris.')->group(function () {
            Route::resource('kategori', InventarisKategoriController::class)->except(['show'])->parameters(['kategori' => 'inventaris_kategori']);
            Route::resource('barang', InventarisBarangController::class)->except(['show'])->parameters(['barang' => 'inventaris_barang']);
            Route::resource('mutasi', InventarisMutasiController::class)->except(['show'])->parameters(['mutasi' => 'inventaris_mutasi']);
        });
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/siswa-csv', [LaporanController::class, 'exportSiswa'])->name('laporan.siswa-csv');
        Route::get('laporan/nilai-csv', [LaporanController::class, 'exportNilai'])->name('laporan.nilai-csv');
        Route::get('laporan/presensi-siswa-csv', [LaporanController::class, 'exportPresensiSiswa'])->name('laporan.presensi-siswa-csv');
        Route::get('laporan/kurikulum-csv', [LaporanController::class, 'exportKurikulum'])->name('laporan.kurikulum-csv');
        Route::get('laporan/tagihan-csv', [LaporanController::class, 'exportTagihan'])->name('laporan.tagihan-csv');
        Route::get('laporan/pembayaran-csv', [LaporanController::class, 'exportPembayaran'])->name('laporan.pembayaran-csv');

        Route::get('presensi', [PresensiHubController::class, 'index'])->name('presensi.index');
        Route::post('ppdb/{ppdb_registration}/jadikan-siswa', [PpdbRegistrationController::class, 'promoteToSiswa'])->name('ppdb.promote-siswa');
        Route::resource('ppdb', PpdbRegistrationController::class)->parameters(['ppdb' => 'ppdb_registration']);

        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('scan/{type}', [PresensiScanController::class, 'show'])->name('scan.show')->where('type', 'siswa|guru|pegawai');
            Route::get('scan/siswa/jadwal-options', [PresensiScanController::class, 'jadwalOptions'])->name('scan.jadwal-options');
            Route::post('scan/{type}/barcode', [PresensiScanController::class, 'barcode'])->name('scan.barcode')->where('type', 'siswa|guru|pegawai');
            Route::post('scan/{type}/face', [PresensiScanController::class, 'face'])->name('scan.face')->where('type', 'siswa|guru|pegawai');
            Route::post('scan/{type}/{person}/face-enroll', [PresensiScanController::class, 'enrollFace'])->name('scan.face-enroll')->where('type', 'siswa|guru|pegawai');
            Route::get('kartu/{type}/{person}', [PresensiScanController::class, 'kartu'])->name('kartu')->where('type', 'siswa|guru|pegawai');

            Route::get('siswa/create', [PresensiSiswaController::class, 'create'])->name('siswa.create');
            Route::post('siswa', [PresensiSiswaController::class, 'store'])->name('siswa.store');
            Route::delete('siswa/{presensiSiswa}', [PresensiSiswaController::class, 'destroy'])->name('siswa.destroy');

            Route::get('guru', [PresensiGuruController::class, 'index'])->name('guru.index');
            Route::get('guru/create', [PresensiGuruController::class, 'create'])->name('guru.create');
            Route::post('guru', [PresensiGuruController::class, 'store'])->name('guru.store');
            Route::delete('guru/{presensiGuru}', [PresensiGuruController::class, 'destroy'])->name('guru.destroy');

            Route::get('pegawai', [PresensiPegawaiController::class, 'index'])->name('pegawai.index');
            Route::get('pegawai/create', [PresensiPegawaiController::class, 'create'])->name('pegawai.create');
            Route::post('pegawai', [PresensiPegawaiController::class, 'store'])->name('pegawai.store');
            Route::delete('pegawai/{presensiPegawai}', [PresensiPegawaiController::class, 'destroy'])->name('pegawai.destroy');
        });
    });

    Route::middleware('role:wali')->group(function () {
        Route::get('wali', [WaliHubController::class, 'index'])->name('wali.index');
        Route::get('wali/{siswa}/keuangan', [WaliKeuanganController::class, 'dashboard'])->name('wali.keuangan.dashboard');
        Route::get('wali/{siswa}/tagihan', [WaliKeuanganController::class, 'index'])->name('wali.tagihan.index');
        Route::get('wali/{siswa}/tagihan/{tagihan}', [WaliKeuanganController::class, 'show'])->name('wali.tagihan.show');
        Route::get('wali/{siswa}', [WaliHubController::class, 'show'])->name('wali.show');
    });
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Deploy Runner — SEMENTARA (hapus setelah deploy tanpa SSH selesai)
|--------------------------------------------------------------------------
| Aktifkan di .env production:
|   DEPLOY_RUNNER_ENABLED=true
|   DEPLOY_RUNNER_TOKEN=token-rahasia-panjang
| Akses: https://domain-anda/_ops/deploy/{DEPLOY_RUNNER_TOKEN}
*/
Route::middleware('throttle:10,1')->group(function (): void {
    Route::get('_ops/deploy/{token}', [DeployRunnerController::class, 'index'])->name('deploy.runner.index');
    Route::post('_ops/deploy/{token}/run', [DeployRunnerController::class, 'run'])->name('deploy.runner.run');
});
