<?php

namespace App\Policies;

use App\Models\InventarisMutasi;
use App\Models\User;
use App\Support\PolicyRoles;

class InventarisMutasiPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, InventarisMutasi $inventarisMutasi): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function update(User $user, InventarisMutasi $inventarisMutasi): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function delete(User $user, InventarisMutasi $inventarisMutasi): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, InventarisMutasi $inventarisMutasi): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, InventarisMutasi $inventarisMutasi): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
