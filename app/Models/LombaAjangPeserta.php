<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LombaAjangPeserta extends Model
{
    protected $fillable = [
        'lomba_ajang_id',
        'siswa_id',
        'juara',
        'prestasi',
        'keterangan',
    ];

    public function lomba(): BelongsTo
    {
        return $this->belongsTo(LombaAjang::class, 'lomba_ajang_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
