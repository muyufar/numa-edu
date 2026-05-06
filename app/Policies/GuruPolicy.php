<?php

namespace App\Policies;

use App\Models\Guru;
use App\Models\User;
use App\Support\PolicyRoles;

class GuruPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, Guru $guru): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, Guru $guru): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, Guru $guru): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Guru $guru): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Guru $guru): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
