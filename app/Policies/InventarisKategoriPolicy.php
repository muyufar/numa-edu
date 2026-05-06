<?php

namespace App\Policies;

use App\Models\InventarisKategori;
use App\Models\User;
use App\Support\PolicyRoles;

class InventarisKategoriPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, InventarisKategori $inventarisKategori): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, InventarisKategori $inventarisKategori): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, InventarisKategori $inventarisKategori): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, InventarisKategori $inventarisKategori): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, InventarisKategori $inventarisKategori): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
