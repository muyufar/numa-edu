<?php

namespace App\Policies;

use App\Models\Kelas;
use App\Models\User;
use App\Support\PolicyRoles;

class KelasPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, Kelas $kelas): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, Kelas $kelas): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, Kelas $kelas): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Kelas $kelas): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Kelas $kelas): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
