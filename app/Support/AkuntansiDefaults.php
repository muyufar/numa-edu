<?php

namespace App\Support;

use App\Models\AkuntansiAkun;

class AkuntansiDefaults
{
    /**
     * Pastikan akun-akun minimum untuk pencatatan pembayaran tersedia.
     *
     * @return array{kas: AkuntansiAkun, pendapatan: AkuntansiAkun, beban: AkuntansiAkun}
     */
    public static function ensureForSekolah(int $sekolahId): array
    {
        if ($sekolahId < 1) {
            $sekolahId = (int) config('tenancy.default_sekolah_id', 1);
        }

        $kas = AkuntansiAkun::withoutGlobalScopes()->firstOrCreate(
            ['sekolah_id' => $sekolahId, 'kode' => '101'],
            ['nama' => 'Kas', 'tipe' => 'aset', 'is_active' => true],
        );

        $pendapatan = AkuntansiAkun::withoutGlobalScopes()->firstOrCreate(
            ['sekolah_id' => $sekolahId, 'kode' => '401'],
            ['nama' => 'Pendapatan SPP', 'tipe' => 'pendapatan', 'is_active' => true],
        );

        $beban = AkuntansiAkun::withoutGlobalScopes()->firstOrCreate(
            ['sekolah_id' => $sekolahId, 'kode' => '501'],
            ['nama' => 'Beban Operasional', 'tipe' => 'beban', 'is_active' => true],
        );

        return [
            'kas' => $kas,
            'pendapatan' => $pendapatan,
            'beban' => $beban,
        ];
    }
}

