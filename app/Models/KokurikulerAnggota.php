<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KokurikulerAnggota extends Model
{
    protected $fillable = [
        'kokurikuler_kegiatan_id',
        'siswa_id',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(KokurikulerKegiatan::class, 'kokurikuler_kegiatan_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
