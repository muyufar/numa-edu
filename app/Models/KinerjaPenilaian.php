<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KinerjaPenilaian extends Model
{
    use BelongsToSekolah;

    public const TARGET_TYPES = ['guru', 'pegawai'];

    protected $fillable = [
        'sekolah_id',
        'target_type',
        'guru_id',
        'pegawai_id',
        'tanggal',
        'periode',
        'aspek',
        'skor',
        'catatan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'skor' => 'integer',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if ($this->guru_id) {
            $sid = Guru::withoutGlobalScopes()->whereKey($this->guru_id)->value('sekolah_id');

            return $sid !== null ? (int) $sid : null;
        }

        if ($this->pegawai_id) {
            $sid = Pegawai::withoutGlobalScopes()->whereKey($this->pegawai_id)->value('sekolah_id');

            return $sid !== null ? (int) $sid : null;
        }

        return null;
    }
}

