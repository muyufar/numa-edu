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
use App\Models\Nilai;
use App\Models\Pegawai;
use App\Models\Pelanggaran;
use App\Models\Perizinan;
use App\Models\PpdbRegistration;
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
        Tagihan::class,
        Berita::class,
        PresensiSiswa::class,
        Perizinan::class,
        KinerjaPenilaian::class,
        InventarisBarang::class,
        Pelanggaran::class,
        PpdbRegistration::class,
    ];

    public static function showModulSection(?User $user = null): bool
    {
        $user ??= auth()->user();
        if (! $user) {
            return false;
        }

        foreach (self::MODUL_POLICY_MODELS as $model) {
            if (Gate::forUser($user)->allows('viewAny', $model)) {
                return true;
            }
        }

        if ($user->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang'])) {
            return true;
        }

        return $user->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']);
    }
}
