<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class KewajibanPembayaran extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const TIPE_OPTIONS = ['bulanan', 'insidental'];

    protected $fillable = [
        'sekolah_id',
        'nama',
        'tipe',
        'nominal_default',
        'berlaku_mulai',
        'batas_hari_bayar',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'nominal_default' => 'decimal:2',
            'batas_hari_bayar' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}

