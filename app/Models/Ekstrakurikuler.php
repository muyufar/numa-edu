<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ekstrakurikuler extends Model
{
    use BelongsToSekolah;

    protected $table = 'ekstrakurikulers';

    protected $fillable = [
        'sekolah_id',
        'nama',
        'guru_id',
        'hari',
        'jam',
        'lokasi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function anggotas(): HasMany
    {
        return $this->hasMany(EkstrakurikulerAnggota::class);
    }

    public function kegiatans(): HasMany
    {
        return $this->hasMany(EkstrakurikulerKegiatan::class);
    }
}
