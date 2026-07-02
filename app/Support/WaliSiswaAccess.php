<?php

namespace App\Support;

use App\Models\Siswa;
use App\Models\User;

final class WaliSiswaAccess
{
    public static function canViewSiswa(User $user, Siswa $siswa): bool
    {
        if (! $user->hasRole('wali')) {
            return false;
        }

        return $user->waliSiswas()->where('siswas.id', $siswa->id)->exists();
    }
}
