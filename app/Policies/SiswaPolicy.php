<?php

namespace App\Policies;

use App\Models\Siswa;
use App\Models\User;
use App\Support\PolicyRoles;

class SiswaPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function deleteAny(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function view(User $user, Siswa $siswa): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, Siswa $siswa): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, Siswa $siswa): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Siswa $siswa): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Siswa $siswa): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
