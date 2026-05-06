<?php

namespace App\Policies;

use App\Models\Jadwal;
use App\Models\User;
use App\Support\PolicyRoles;

class JadwalPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, Jadwal $jadwal): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, Jadwal $jadwal): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, Jadwal $jadwal): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Jadwal $jadwal): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Jadwal $jadwal): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
