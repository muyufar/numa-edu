<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use App\Models\Concerns\HasPresensiKode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    use BelongsToSekolah;
    use HasPresensiKode;

    protected $fillable = [
        'sekolah_id',
        'user_id',
        'presensi_kode',
        'face_descriptor',
        'nip',
        'nama',
        'phone',
        'jenis_kelamin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function presensiGurus(): HasMany
    {
        return $this->hasMany(PresensiGuru::class);
    }

    protected function casts(): array
    {
        return [
            'face_descriptor' => 'array',
        ];
    }

    protected static function presensiKodePrefix(): string
    {
        return 'GRU';
    }
}
