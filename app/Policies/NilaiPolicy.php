<?php

namespace App\Policies;

use App\Models\Nilai;
use App\Models\User;
use App\Support\PolicyRoles;

class NilaiPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, Nilai $nilai): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, Nilai $nilai): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function delete(User $user, Nilai $nilai): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Nilai $nilai): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Nilai $nilai): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
