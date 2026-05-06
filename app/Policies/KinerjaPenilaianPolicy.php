<?php

namespace App\Policies;

use App\Models\KinerjaPenilaian;
use App\Models\User;
use App\Support\PolicyRoles;

class KinerjaPenilaianPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, KinerjaPenilaian $kinerjaPenilaian): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, KinerjaPenilaian $kinerjaPenilaian): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, KinerjaPenilaian $kinerjaPenilaian): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, KinerjaPenilaian $kinerjaPenilaian): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, KinerjaPenilaian $kinerjaPenilaian): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
