<?php

namespace App\Policies;

use App\Models\InventarisBarang;
use App\Models\User;
use App\Support\PolicyRoles;

class InventarisBarangPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, InventarisBarang $inventarisBarang): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, InventarisBarang $inventarisBarang): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, InventarisBarang $inventarisBarang): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, InventarisBarang $inventarisBarang): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, InventarisBarang $inventarisBarang): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
