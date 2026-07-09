<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TugasPengumpulan extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'tugas_id',
        'siswa_id',
        'jawaban_esai',
        'file_path',
        'file_name',
        'mime',
        'size',
        'nilai_otomatis',
        'status',
        'dikumpulkan_pada',
    ];

    protected function casts(): array
    {
        return [
            'nilai_otomatis' => 'integer',
            'size' => 'integer',
            'dikumpulkan_pada' => 'datetime',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->siswa_id) {
            return null;
        }

        $sid = Siswa::withoutGlobalScopes()->whereKey($this->siswa_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jawabanPilihans(): HasMany
    {
        return $this->hasMany(TugasJawabanPilihan::class);
    }
}
