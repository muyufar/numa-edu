<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasPilihan extends Model
{
    protected $table = 'tugas_pilihans';

    protected $fillable = [
        'tugas_soal_id',
        'label',
        'teks',
        'is_benar',
    ];

    protected function casts(): array
    {
        return [
            'is_benar' => 'boolean',
        ];
    }

    public function tugasSoal(): BelongsTo
    {
        return $this->belongsTo(TugasSoal::class);
    }
}
