<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'kode',
        'nama',
    ];

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    public function kurikulumItems(): HasMany
    {
        return $this->hasMany(KurikulumItem::class);
    }
}
