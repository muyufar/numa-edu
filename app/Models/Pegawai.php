<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use App\Models\Concerns\HasPresensiKode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use BelongsToSekolah;
    use HasPresensiKode;

    protected $table = 'pegawais';

    protected $fillable = [
        'sekolah_id',
        'presensi_kode',
        'face_descriptor',
        'nama',
        'nip',
        'jabatan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'face_descriptor' => 'array',
        ];
    }

    protected static function presensiKodePrefix(): string
    {
        return 'PEG';
    }

    public function presensiPegawais(): HasMany
    {
        return $this->hasMany(PresensiPegawai::class);
    }
}
