<?php

namespace App\Policies;

use App\Models\PresensiGuru;
use App\Models\User;
use App\Support\PolicyRoles;

class PresensiGuruPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, PresensiGuru $presensiGuru): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, PresensiGuru $presensiGuru): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function delete(User $user, PresensiGuru $presensiGuru): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, PresensiGuru $presensiGuru): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, PresensiGuru $presensiGuru): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
