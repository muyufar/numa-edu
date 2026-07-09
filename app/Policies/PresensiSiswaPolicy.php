<?php

namespace App\Policies;

use App\Models\PresensiSiswa;
use App\Models\User;
use App\Support\PolicyRoles;

class PresensiSiswaPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user) || PolicyRoles::siswaTerhubung($user);
    }

    public function view(User $user, PresensiSiswa $presensiSiswa): bool
    {
        if (PolicyRoles::akademikTim($user)) {
            return true;
        }

        if (PolicyRoles::siswaTerhubung($user)) {
            return (int) $presensiSiswa->siswa_id === (int) $user->siswa?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, PresensiSiswa $presensiSiswa): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function delete(User $user, PresensiSiswa $presensiSiswa): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, PresensiSiswa $presensiSiswa): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, PresensiSiswa $presensiSiswa): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
