<?php

namespace App\Policies;

use App\Models\Perizinan;
use App\Models\User;
use App\Support\PolicyRoles;

class PerizinanPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, Perizinan $perizinan): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, Perizinan $perizinan): bool
    {
        if (PolicyRoles::adminTim($user)) {
            return true;
        }

        return $user->hasRole('guru') && $perizinan->status === 'pending';
    }

    public function delete(User $user, Perizinan $perizinan): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Perizinan $perizinan): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Perizinan $perizinan): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
