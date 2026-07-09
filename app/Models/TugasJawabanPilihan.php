<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasJawabanPilihan extends Model
{
    protected $fillable = [
        'tugas_pengumpulan_id',
        'tugas_soal_id',
        'tugas_pilihan_id',
        'is_benar',
    ];

    protected function casts(): array
    {
        return [
            'is_benar' => 'boolean',
        ];
    }

    public function pengumpulan(): BelongsTo
    {
        return $this->belongsTo(TugasPengumpulan::class, 'tugas_pengumpulan_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(TugasSoal::class, 'tugas_soal_id');
    }

    public function pilihan(): BelongsTo
    {
        return $this->belongsTo(TugasPilihan::class, 'tugas_pilihan_id');
    }
}
