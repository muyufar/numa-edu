<?php

namespace App\Policies;

use App\Models\Tagihan;
use App\Models\User;
use App\Support\PolicyRoles;
use App\Support\WaliSiswaAccess;

class TagihanPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function view(User $user, Tagihan $tagihan): bool
    {
        if (PolicyRoles::adminTim($user)) {
            return true;
        }

        $tagihan->loadMissing('siswa');

        return $tagihan->siswa !== null && WaliSiswaAccess::canViewSiswa($user, $tagihan->siswa);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, Tagihan $tagihan): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, Tagihan $tagihan): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Tagihan $tagihan): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Tagihan $tagihan): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
