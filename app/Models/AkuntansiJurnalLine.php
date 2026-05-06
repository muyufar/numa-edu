<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkuntansiJurnalLine extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'jurnal_id',
        'akun_id',
        'debit',
        'kredit',
    ];

    protected function casts(): array
    {
        return [
            'debit' => 'decimal:2',
            'kredit' => 'decimal:2',
        ];
    }

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(AkuntansiJurnal::class, 'jurnal_id');
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(AkuntansiAkun::class, 'akun_id');
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if ($this->getAttribute('sekolah_id')) {
            return (int) $this->getAttribute('sekolah_id');
        }

        if ($this->jurnal_id) {
            $sid = AkuntansiJurnal::withoutGlobalScopes()->whereKey($this->jurnal_id)->value('sekolah_id');

            return $sid !== null ? (int) $sid : null;
        }

        return null;
    }
}

