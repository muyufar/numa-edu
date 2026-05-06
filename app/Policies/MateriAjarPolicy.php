<?php

namespace App\Policies;

use App\Models\MateriAjar;
use App\Models\User;
use App\Support\PolicyRoles;

class MateriAjarPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'guru', 'siswa', 'wali'])
            || $user->isPengurusSekolahAktif();
    }

    public function view(User $user, MateriAjar $materiAjar): bool
    {
        if (PolicyRoles::akademikTim($user)) {
            return true;
        }

        if ($user->hasRole('siswa')) {
            $kelasId = $user->siswa?->kelas_id ?? null;

            return $materiAjar->kelas_id === null || ($kelasId !== null && $materiAjar->kelas_id === $kelasId);
        }

        if ($user->hasRole('wali')) {
            if ($materiAjar->kelas_id === null) {
                return true;
            }

            $kelasIds = $user->waliSiswas()->pluck('kelas_id')->filter()->unique()->values();

            return $kelasIds->contains($materiAjar->kelas_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, MateriAjar $materiAjar): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function delete(User $user, MateriAjar $materiAjar): bool
    {
        return PolicyRoles::akademikTim($user);
    }
}
