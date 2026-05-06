<?php

namespace App\Policies;

use App\Models\KurikulumItem;
use App\Models\User;
use App\Support\PolicyRoles;

class KurikulumItemPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function view(User $user, KurikulumItem $kurikulumItem): bool
    {
        return PolicyRoles::akademikTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, KurikulumItem $kurikulumItem): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, KurikulumItem $kurikulumItem): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
