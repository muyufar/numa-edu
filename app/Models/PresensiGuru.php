<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiGuru extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const STATUS_OPTIONS = [
        'hadir',
        'izin',
        'sakit',
        'alpa',
    ];

    /** @var list<string> */
    public const METODE_OPTIONS = [
        'manual',
        'barcode',
        'face',
    ];

    protected $fillable = [
        'sekolah_id',
        'guru_id',
        'tanggal',
        'status',
        'metode',
        'jam_masuk',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->guru_id) {
            return null;
        }

        $sid = Guru::withoutGlobalScopes()->whereKey($this->guru_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }
}
