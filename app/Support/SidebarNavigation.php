<?php

namespace App\Support;

use App\Models\Berita;
use App\Models\Guru;
use App\Models\InventarisBarang;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\KinerjaPenilaian;
use App\Models\KurikulumItem;
use App\Models\MataPelajaran;
use App\Models\MateriAjar;
use App\Models\Tugas;
use App\Models\Nilai;
use App\Models\Pegawai;
use App\Models\Pelanggaran;
use App\Models\Perizinan;
use App\Models\PpdbRegistration;
use App\Models\PerpustakaanBuku;
use App\Models\PresensiGuru;
use App\Models\PresensiPegawai;
use App\Models\PresensiSiswa;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class SidebarNavigation
{
    /** @var list<class-string> */
    private const MODUL_POLICY_MODELS = [
        Kelas::class,
        MataPelajaran::class,
        KurikulumItem::class,
        Siswa::class,
        Guru::class,
        Pegawai::class,
        Jadwal::class,
        Nilai::class,
        MateriAjar::class,
        Tugas::class,
        Tagihan::class,
        Berita::class,
        PresensiSiswa::class,
        PresensiGuru::class,
        PresensiPegawai::class,
        Perizinan::class,
        KinerjaPenilaian::class,
        InventarisBarang::class,
        PerpustakaanBuku::class,
        Pelanggaran::class,
        PpdbRegistration::class,
    ];

    public static function showModulSection(?User $user = null): bool
    {
        $user ??= auth()->user();
        if (! $user) {
            return false;
        }

        if (self::showKurikulumMenu($user) || self::showSiswaMenu($user) || self::showGuruMenu($user)) {
            return true;
        }

        foreach ([Tagihan::class, Berita::class, InventarisBarang::class, PerpustakaanBuku::class, Pelanggaran::class] as $model) {
            if (Gate::forUser($user)->allows('viewAny', $model)) {
                return true;
            }
        }

        if ($user->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang'])) {
            return true;
        }

        return $user->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']);
    }

    public static function showKurikulumMenu(?User $user = null): bool
    {
        return self::allowsAny($user, [
            Kelas::class,
            MataPelajaran::class,
            KurikulumItem::class,
            Jadwal::class,
            Nilai::class,
            MateriAjar::class,
            Tugas::class,
        ]);
    }

    public static function showSiswaMenu(?User $user = null): bool
    {
        $user ??= auth()->user();
        if (! $user) {
            return false;
        }

        if (self::allowsAny($user, [Siswa::class, PpdbRegistration::class, PresensiSiswa::class, Perizinan::class])) {
            return true;
        }

        return $user->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang']);
    }

    public static function showGuruMenu(?User $user = null): bool
    {
        return self::allowsAny($user, [
            Guru::class,
            Pegawai::class,
            KinerjaPenilaian::class,
            PresensiGuru::class,
            PresensiPegawai::class,
        ]);
    }

    /**
     * @param  list<class-string>  $models
     */
    private static function allowsAny(?User $user, array $models): bool
    {
        $user ??= auth()->user();
        if (! $user) {
            return false;
        }

        foreach ($models as $model) {
            if (Gate::forUser($user)->allows('viewAny', $model)) {
                return true;
            }
        }

        return false;
    }
}
