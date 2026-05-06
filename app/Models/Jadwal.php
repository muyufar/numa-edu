<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const HARI_OPTIONS = [
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
        'Minggu',
    ];

    protected $fillable = [
        'sekolah_id',
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'tahun_ajaran',
    ];

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->kelas_id) {
            return null;
        }

        $sid = Kelas::withoutGlobalScopes()->whereKey($this->kelas_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        $cases = collect(self::HARI_OPTIONS)
            ->map(fn (string $h, int $i) => "WHEN '{$h}' THEN ".($i + 1))
            ->implode(' ');

        return $query
            ->orderByRaw("CASE jadwals.hari {$cases} ELSE 99 END")
            ->orderBy('jadwals.jam_mulai');
    }
}
