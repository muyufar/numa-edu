<?php

use App\Http\Controllers\AkunOnboardingController;
use App\Http\Controllers\AkuntansiAkunController;
use App\Http\Controllers\AkuntansiController;
use App\Http\Controllers\AkuntansiJurnalController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\BeritaPublicController;
use App\Http\Controllers\BukuKasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\InventarisBarangController;
use App\Http\Controllers\InventarisKategoriController;
use App\Http\Controllers\InventarisMutasiController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KeuanganHubController;
use App\Http\Controllers\KeuanganRekapController;
use App\Http\Controllers\KeuanganTunggakanController;
use App\Http\Controllers\KewajibanPembayaranController;
use App\Http\Controllers\KinerjaPenilaianController;
use App\Http\Controllers\KurikulumItemController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LembagaRegistrationNpsnLookupController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\MateriAjarController;
use App\Http\Controllers\MouLembagaPublicController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\NominatimProxyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PendaftaranLembagaPublicController;
use App\Http\Controllers\PendaftaranPpdbController;
use App\Http\Controllers\PengeluaranKasController;
use App\Http\Controllers\PengurusCabang\LembagaMouCabangSettingsController;
use App\Http\Controllers\PengurusCabang\LembagaRegistrationController;
use App\Http\Controllers\PengurusCabang\SekolahPendaftaranController;
use App\Http\Controllers\PengurusCabang\SekolahPilihController;
use App\Http\Controllers\PengurusCabang\SekolahProfilController;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\PpdbRegistrationController;
use App\Http\Controllers\PresensiGuruController;
use App\Http\Controllers\PresensiHubController;
use App\Http\Controllers\PresensiPegawaiController;
use App\Http\Controllers\PresensiSiswaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProsesPembayaranController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SekolahLembagaProfilController;
use App\Http\Controllers\SiswaAkunAdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\WaliAdminController;
use App\Http\Controllers\WaliHubController;
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

    Route::middleware('role:super_admin|admin|pengurus_cabang')->group(function () {
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
        Route::delete('keuangan/pengeluaran-kas/{pengeluaran_kas}', [PengeluaranKasController::class, 'destroy'])->name('keuangan.pengeluaran-kas.destroy');

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
    Route::resource('materi', MateriAjarController::class)->except(['show'])->parameters(['materi' => 'materi_ajar']);

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
        Route::resource('siswa', SiswaController::class)->except(['show']);
        Route::post('siswa/{siswa}/buat-akun', [SiswaController::class, 'buatAkun'])->name('siswa.buat-akun');
        Route::put('siswa/{siswa}/akun', [SiswaController::class, 'updateAkun'])->name('siswa.akun.update');
        Route::post('siswa/{siswa}/akun/reset-password', [SiswaController::class, 'resetPasswordAkun'])->name('siswa.akun.reset-password');
        Route::get('siswa/{siswa}/wali', [WaliSiswaController::class, 'edit'])->name('siswa.wali.edit');
        Route::post('siswa/{siswa}/wali', [WaliSiswaController::class, 'store'])->name('siswa.wali.store');
        Route::post('siswa/{siswa}/wali/buat-akun', [WaliSiswaController::class, 'buatAkunWali'])->name('siswa.wali.buat-akun');
        Route::delete('siswa/{siswa}/wali/{user}', [WaliSiswaController::class, 'destroy'])->name('siswa.wali.destroy');
        Route::resource('guru', GuruController::class)->except(['show']);
        Route::resource('pegawai', PegawaiController::class)->except(['show']);
        Route::resource('jadwal', JadwalController::class)->except(['show']);

        Route::get('nilai/bulk', [NilaiController::class, 'bulkCreate'])->name('nilai.bulk.create');
        Route::post('nilai/bulk', [NilaiController::class, 'bulkStore'])->name('nilai.bulk.store');
        Route::resource('nilai', NilaiController::class)->except(['show']);
        Route::resource('bk/pelanggaran', PelanggaranController::class)->except(['show'])->names('bk.pelanggaran');

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
            Route::get('siswa', [PresensiSiswaController::class, 'index'])->name('siswa.index');
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
        Route::get('wali/{siswa}', [WaliHubController::class, 'show'])->name('wali.show');
    });
});

require __DIR__.'/auth.php';
