<?php

namespace App\Policies;

use App\Models\PerpustakaanBuku;
use App\Models\User;
use App\Support\PolicyRoles;

class PerpustakaanBukuPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::bisaAksesPerpustakaan($user);
    }

    public function view(User $user, PerpustakaanBuku $buku): bool
    {
        return PolicyRoles::bisaAksesPerpustakaan($user);
    }

    public function create(User $user): bool
    {
        return PolicyRoles::perpusTim($user);
    }

    public function update(User $user, PerpustakaanBuku $buku): bool
    {
        return PolicyRoles::perpusTim($user);
    }

    public function delete(User $user, PerpustakaanBuku $buku): bool
    {
        return PolicyRoles::perpusTim($user);
    }

    public function preview(User $user, PerpustakaanBuku $buku): bool
    {
        if (PolicyRoles::perpusTim($user)) {
            return true;
        }

        return $buku->userHasAksesDigital($user);
    }

    public function pinjam(User $user, PerpustakaanBuku $buku): bool
    {
        return PolicyRoles::peminjam($user) || PolicyRoles::perpusTim($user);
    }
}
