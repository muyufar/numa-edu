<?php

namespace App\Policies;

use App\Models\Pelanggaran;
use App\Models\User;
use App\Support\PolicyRoles;

class PelanggaranPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, Pelanggaran $pelanggaran): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, Pelanggaran $pelanggaran): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function delete(User $user, Pelanggaran $pelanggaran): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Pelanggaran $pelanggaran): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Pelanggaran $pelanggaran): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
