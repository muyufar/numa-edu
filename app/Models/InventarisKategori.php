<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventarisKategori extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'nama',
        'deskripsi',
    ];

    public function barangs(): HasMany
    {
        return $this->hasMany(InventarisBarang::class);
    }
}
