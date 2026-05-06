<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const SEMESTER_OPTIONS = ['1', '2'];

    protected $fillable = [
        'sekolah_id',
        'siswa_id',
        'mata_pelajaran_id',
        'kelas_id',
        'semester',
        'tahun_ajaran',
        'nilai_akhir',
    ];

    protected function casts(): array
    {
        return [
            'nilai_akhir' => 'decimal:2',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if ($this->siswa_id) {
            $sid = Siswa::withoutGlobalScopes()->whereKey($this->siswa_id)->value('sekolah_id');

            return $sid !== null ? (int) $sid : null;
        }

        if ($this->kelas_id) {
            $sid = Kelas::withoutGlobalScopes()->whereKey($this->kelas_id)->value('sekolah_id');

            return $sid !== null ? (int) $sid : null;
        }

        return null;
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
