<?php

namespace App\Support;

use App\Models\User;

final class PolicyRoles
{
    public static function adminTim(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->isPengurusSekolahAktif();
    }

    public static function akademikTim(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'guru']) || $user->isPengurusSekolahAktif();
    }
}
