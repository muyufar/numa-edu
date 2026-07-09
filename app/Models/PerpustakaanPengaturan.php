<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class PerpustakaanPengaturan extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'max_peminjaman_aktif',
        'masa_pinjam_fisik_hari',
        'masa_pinjam_digital_hari',
        'denda_per_hari',
        'max_perpanjangan',
    ];

    protected function casts(): array
    {
        return [
            'max_peminjaman_aktif' => 'integer',
            'masa_pinjam_fisik_hari' => 'integer',
            'masa_pinjam_digital_hari' => 'integer',
            'denda_per_hari' => 'integer',
            'max_perpanjangan' => 'integer',
        ];
    }

    public static function forSekolah(int $sekolahId): self
    {
        return self::withoutGlobalScopes()->firstOrCreate(
            ['sekolah_id' => $sekolahId],
            [
                'max_peminjaman_aktif' => 3,
                'masa_pinjam_fisik_hari' => 7,
                'masa_pinjam_digital_hari' => 14,
                'denda_per_hari' => 1000,
                'max_perpanjangan' => 1,
            ]
        );
    }
}
