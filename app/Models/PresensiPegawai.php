<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiPegawai extends Model
{
    use BelongsToSekolah;

    protected $table = 'presensi_pegawais';

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
        'pegawai_id',
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
        if (! $this->pegawai_id) {
            return null;
        }

        $sid = Pegawai::withoutGlobalScopes()->whereKey($this->pegawai_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
