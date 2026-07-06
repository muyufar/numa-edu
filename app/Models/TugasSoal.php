<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TugasSoal extends Model
{
    protected $fillable = [
        'tugas_id',
        'urutan',
        'pertanyaan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }

    public function pilihans(): HasMany
    {
        return $this->hasMany(TugasPilihan::class)->orderBy('label');
    }
}
