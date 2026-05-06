<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'user_id',
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

    public function presensiGurus(): HasMany
    {
        return $this->hasMany(PresensiGuru::class);
    }
}
