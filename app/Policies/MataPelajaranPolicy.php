<?php

namespace App\Policies;

use App\Models\MataPelajaran;
use App\Models\User;
use App\Support\PolicyRoles;

class MataPelajaranPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, MataPelajaran $mataPelajaran): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, MataPelajaran $mataPelajaran): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, MataPelajaran $mataPelajaran): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, MataPelajaran $mataPelajaran): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, MataPelajaran $mataPelajaran): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
