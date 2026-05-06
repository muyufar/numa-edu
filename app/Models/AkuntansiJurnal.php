<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AkuntansiJurnal extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'tanggal',
        'no_bukti',
        'keterangan',
        'sumber_type',
        'sumber_id',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(AkuntansiJurnalLine::class, 'jurnal_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function sumber(): MorphTo
    {
        return $this->morphTo();
    }
}

