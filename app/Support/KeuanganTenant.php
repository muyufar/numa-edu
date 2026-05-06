<?php

namespace App\Support;

use App\Models\User;

/**
 * Menyelaraskan pemilihan sekolah aktif untuk modul keuangan dengan {@see \App\Models\Concerns\BelongsToSekolah::defaultSekolahIdForNewRow}.
 */
class KeuanganTenant
{
    public static function sekolahId(?User $user): int
    {
        if ($user?->hasRole('pengurus_cabang') && session('pengurus_sekolah_id')) {
            return (int) session('pengurus_sekolah_id');
        }

        if ($user && (int) $user->sekolah_id > 0) {
            return (int) $user->sekolah_id;
        }

        return (int) config('tenancy.default_sekolah_id', 1);
    }
}
