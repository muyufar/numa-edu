<?php

namespace App\Policies;

use App\Models\Pembayaran;
use App\Models\User;
use App\Support\PolicyRoles;

class PembayaranPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function view(User $user, Pembayaran $pembayaran): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function update(User $user, Pembayaran $pembayaran): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function delete(User $user, Pembayaran $pembayaran): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function restore(User $user, Pembayaran $pembayaran): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function forceDelete(User $user, Pembayaran $pembayaran): bool
    {
        return PolicyRoles::adminTim($user);
    }
}
