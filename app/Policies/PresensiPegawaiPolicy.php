<?php

namespace App\Policies;

use App\Models\PresensiPegawai;
use App\Models\User;
use App\Support\PolicyRoles;

class PresensiPegawaiPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, PresensiPegawai $presensiPegawai): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, PresensiPegawai $presensiPegawai): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function delete(User $user, PresensiPegawai $presensiPegawai): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, PresensiPegawai $presensiPegawai): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, PresensiPegawai $presensiPegawai): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
