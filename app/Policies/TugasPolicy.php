<?php

namespace App\Policies;

use App\Models\Tugas;
use App\Models\TugasPengumpulan;
use App\Models\User;
use App\Support\PolicyRoles;

class TugasPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'guru', 'siswa', 'wali'])
            || $user->isPengurusSekolahAktif();
    }

    public function view(User $user, Tugas $tugas): bool
    {
        if (! $tugas->is_published && ! PolicyRoles::akademikTim($user)) {
            return false;
        }

        if (PolicyRoles::akademikTim($user)) {
            return true;
        }

        if ($user->hasRole('siswa')) {
            $kelasId = $user->siswa?->kelas_id ?? null;

            return $tugas->kelas_id === null || ($kelasId !== null && $tugas->kelas_id === $kelasId);
        }

        if ($user->hasRole('wali')) {
            if ($tugas->kelas_id === null) {
                return true;
            }

            $kelasIds = $user->waliSiswas()->pluck('kelas_id')->filter()->unique()->values();

            return $kelasIds->contains($tugas->kelas_id);
        }

        return false;
    }

    public function submit(User $user, Tugas $tugas): bool
    {
        if (! PolicyRoles::siswaTerhubung($user)) {
            return false;
        }

        if (! $this->view($user, $tugas)) {
            return false;
        }

        if ($tugas->isOverdue()) {
            return false;
        }

        return ! TugasPengumpulan::query()
            ->where('tugas_id', $tugas->id)
            ->where('siswa_id', $user->siswa?->id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, Tugas $tugas): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function delete(User $user, Tugas $tugas): bool
    {
        return PolicyRoles::akademikTim($user);
    }
}
