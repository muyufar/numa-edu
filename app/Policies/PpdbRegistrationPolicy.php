<?php

namespace App\Policies;

use App\Models\PpdbRegistration;
use App\Models\User;
use App\Support\PolicyRoles;

class PpdbRegistrationPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, PpdbRegistration $ppdbRegistration): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, PpdbRegistration $ppdbRegistration): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, PpdbRegistration $ppdbRegistration): bool
    {
        if (! PolicyRoles::adminTim($user)) {
            return false;
        }

        return ! $ppdbRegistration->siswa()->exists();
    }

    public function restore(User $user, PpdbRegistration $ppdbRegistration): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, PpdbRegistration $ppdbRegistration): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
