<?php

namespace App\Policies;

use App\Models\PerpustakaanPengaturan;
use App\Models\User;
use App\Support\PolicyRoles;

class PerpustakaanPengaturanPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::perpusTim($user);
    }

    public function update(User $user, PerpustakaanPengaturan $pengaturan): bool
    {
        return PolicyRoles::perpusTim($user);
    }
}
