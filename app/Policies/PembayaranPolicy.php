<?php

namespace App\Policies;

use App\Models\Pembayaran;
use App\Models\User;
use App\Support\PolicyRoles;
use App\Support\WaliSiswaAccess;

class PembayaranPolicy
{
    public function viewAny(User $user): bool
    {
        return PolicyRoles::adminTim($user);
    }

    public function view(User $user, Pembayaran $pembayaran): bool
    {
        if (PolicyRoles::adminTim($user)) {
            return true;
        }

        $pembayaran->loadMissing('tagihan.siswa');
        $siswa = $pembayaran->tagihan?->siswa;

        return $siswa !== null && WaliSiswaAccess::canViewSiswa($user, $siswa);
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
