<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\User;
use App\Support\PolicyRoles;

class PegawaiPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, Pegawai $pegawai): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, Pegawai $pegawai): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, Pegawai $pegawai): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Pegawai $pegawai): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Pegawai $pegawai): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
