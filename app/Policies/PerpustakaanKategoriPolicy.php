<?php

namespace App\Policies;

use App\Models\PerpustakaanKategori;
use App\Models\User;
use App\Support\PolicyRoles;

class PerpustakaanKategoriPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::perpusTim($user);
    }

    public function view(User $user, PerpustakaanKategori $kategori): bool
    {
        return PolicyRoles::perpusTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::perpusTim($user);
    }

    public function update(User $user, PerpustakaanKategori $kategori): bool
    {
        return PolicyRoles::perpusTim($user);
    }

    public function delete(User $user, PerpustakaanKategori $kategori): bool
    {
        return PolicyRoles::perpusTim($user);
    }
}
