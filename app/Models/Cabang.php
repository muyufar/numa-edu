<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    protected $fillable = [
        'nama',
        'kode',
    ];

    public function sekolahs(): HasMany
    {
        return $this->hasMany(Sekolah::class);
    }
}
