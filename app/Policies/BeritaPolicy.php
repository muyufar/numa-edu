<?php

namespace App\Policies;

use App\Models\Berita;
use App\Models\User;
use App\Support\PolicyRoles;

class BeritaPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function view(User $user, Berita $berita): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, Berita $berita): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, Berita $berita): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Berita $berita): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Berita $berita): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
