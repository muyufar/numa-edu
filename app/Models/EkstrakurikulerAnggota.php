<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EkstrakurikulerAnggota extends Model
{
    protected $fillable = [
        'ekstrakurikuler_id',
        'siswa_id',
        'peran',
        'bergabung_tanggal',
    ];

    protected function casts(): array
    {
        return [
            'bergabung_tanggal' => 'date',
        ];
    }

    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
