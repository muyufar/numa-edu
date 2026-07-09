<?php

namespace App\Support;

use App\Models\User;

final class PolicyRoles
{
    public static function adminTim(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->isPengurusSekolahAktif();
    }

    public static function perpusTim(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'admin_perpus']) || $user->isPengurusSekolahAktif();
    }

    public static function peminjam(User $user): bool
    {
        return $user->hasAnyRole(['siswa', 'guru']);
    }

    public static function bisaAksesPerpustakaan(User $user): bool
    {
        return self::perpusTim($user) || self::peminjam($user);
    }

    public static function akademikTim(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'guru']) || $user->isPengurusSekolahAktif();
    }
}
