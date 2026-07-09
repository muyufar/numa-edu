<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LombaAjang extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'nama',
        'tingkat',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'penyelenggara',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function pesertas(): HasMany
    {
        return $this->hasMany(LombaAjangPeserta::class);
    }
}
