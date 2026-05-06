<?php

namespace App\Policies;

use App\Models\Sekolah;
use App\Models\User;

class SekolahPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('pengurus_cabang');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('pengurus_cabang');
    }

    public function view(User $user, Sekolah $sekolah): bool
    {
        return $this->update($user, $sekolah);
    }

    public function update(User $user, Sekolah $sekolah): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('admin') && $user->sekolah_id) {
            return (int) $sekolah->id === (int) $user->sekolah_id;
        }

        return $user->hasRole('pengurus_cabang')
            && $user->cabang_id
            && (int) $sekolah->cabang_id === (int) $user->cabang_id;
    }
}
