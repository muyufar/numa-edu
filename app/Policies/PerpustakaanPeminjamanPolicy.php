<?php

namespace App\Policies;

use App\Models\PerpustakaanPeminjaman;
use App\Models\User;
use App\Support\PolicyRoles;

class PerpustakaanPeminjamanPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::bisaAksesPerpustakaan($user);
    }

    public function view(User $user, PerpustakaanPeminjaman $peminjaman): bool
    {
        if (PolicyRoles::perpusTim($user)) {
            return true;
        }

        return (int) $peminjaman->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return PolicyRoles::peminjam($user) || PolicyRoles::perpusTim($user);
    }

    public function kembalikan(User $user, PerpustakaanPeminjaman $peminjaman): bool
    {
        return PolicyRoles::perpusTim($user);
    }

    public function perpanjang(User $user, PerpustakaanPeminjaman $peminjaman): bool
    {
        if (PolicyRoles::perpusTim($user)) {
            return true;
        }

        return (int) $peminjaman->user_id === (int) $user->id && $peminjaman->isAktif();
    }

    public function tandaiHilang(User $user, PerpustakaanPeminjaman $peminjaman): bool
    {
        return PolicyRoles::perpusTim($user);
    }
}
