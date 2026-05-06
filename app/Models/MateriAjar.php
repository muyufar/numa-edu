<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MateriAjar extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'mata_pelajaran_id',
        'kelas_id',
        'guru_id',
        'judul',
        'deskripsi',
        'semester',
        'tahun_ajaran',
        'tanggal',
        'file_path',
        'file_name',
        'mime',
        'size',
        'diunggah_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'size' => 'integer',
    ];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function diunggahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if ($this->kelas_id) {
            $sid = Kelas::withoutGlobalScopes()->whereKey($this->kelas_id)->value('sekolah_id');

            return $sid !== null ? (int) $sid : null;
        }

        if ($this->mata_pelajaran_id) {
            $sid = MataPelajaran::withoutGlobalScopes()->whereKey($this->mata_pelajaran_id)->value('sekolah_id');

            return $sid !== null ? (int) $sid : null;
        }

        return null;
    }
}

