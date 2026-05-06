<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use BelongsToSekolah;

    protected $table = 'pegawais';

    protected $fillable = [
        'sekolah_id',
        'nama',
        'nip',
        'jabatan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function presensiPegawais(): HasMany
    {
        return $this->hasMany(PresensiPegawai::class);
    }
}
