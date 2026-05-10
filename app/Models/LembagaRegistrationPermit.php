<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LembagaRegistrationPermit extends Model
{
    protected $fillable = [
        'lembaga_registration_id',
        'sort_order',
        'permit_key',
        'nama_sk',
        'nomor_sk',
        'tanggal_sk',
        'dokumen_path',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(LembagaRegistration::class, 'lembaga_registration_id');
    }
}
