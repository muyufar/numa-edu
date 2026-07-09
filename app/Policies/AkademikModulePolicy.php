<?php

namespace App\Policies;

use App\Models\User;
use App\Support\PolicyRoles;

abstract class AkademikModulePolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, mixed $model): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, mixed $model): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function delete(User $user, mixed $model): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
