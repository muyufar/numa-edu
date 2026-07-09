<?php

namespace App\Support;

use App\Models\Scopes\TenantScope;
use App\Models\User;

final class PerpustakaanTenant
{
    /**
     * ID sekolah aktif untuk modul perpustakaan (pengaturan, konteks tenant).
     */
    public static function sekolahId(?User $user = null): int
    {
        $user ??= auth()->user();

        if ($user?->hasRole('pengurus_cabang') && session('pengurus_sekolah_id')) {
            return (int) session('pengurus_sekolah_id');
        }

        $effective = TenantScope::effectiveSekolahId();

        if (is_int($effective) && $effective > 0) {
            return $effective;
        }

        if ($user && (int) $user->sekolah_id > 0) {
            return (int) $user->sekolah_id;
        }

        return (int) config('tenancy.default_sekolah_id', 1);
    }

    public static function pengurusCabangNeedsPilihSekolah(?User $user = null): bool
    {
        $user ??= auth()->user();

        return $user?->hasRole('pengurus_cabang') && ! session('pengurus_sekolah_id');
    }
}
