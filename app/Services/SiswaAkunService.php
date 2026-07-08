<?php

namespace App\Services;

use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use App\Support\SiswaAkunEmail;
use Illuminate\Support\Facades\Hash;

class SiswaAkunService
{
    public function provision(Siswa $siswa, ?string $password = null): ?User
    {
        if ($siswa->user_id) {
            return $siswa->user;
        }

        $email = SiswaAkunEmail::fromNisn($siswa->nisn);
        if (! $email) {
            return null;
        }

        if (User::query()->where('email', $email)->exists()) {
            return null;
        }

        $nisn = SiswaAkunEmail::normalizeNisn($siswa->nisn);
        $plainPassword = $password ?: $nisn;

        $cabangId = Sekolah::withoutGlobalScopes()
            ->whereKey($siswa->sekolah_id)
            ->value('cabang_id');

        $user = User::query()->create([
            'name' => $siswa->nama,
            'email' => $email,
            'jenis_akun' => 'siswa',
            'password' => Hash::make($plainPassword),
            'sekolah_id' => $siswa->sekolah_id,
            'cabang_id' => $cabangId,
        ]);

        $user->assignRole('siswa');

        $siswa->forceFill(['user_id' => $user->id])->saveQuietly();

        return $user;
    }
}
