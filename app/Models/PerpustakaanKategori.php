<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerpustakaanKategori extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'nama',
        'kode',
        'deskripsi',
    ];

    public function bukus(): HasMany
    {
        return $this->hasMany(PerpustakaanBuku::class);
    }
}
