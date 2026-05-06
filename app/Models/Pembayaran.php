<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const METODE_OPTIONS = [
        'tunai',
        'transfer',
        'virtual',
        'lainnya',
    ];

    protected $fillable = [
        'sekolah_id',
        'tagihan_id',
        'jumlah',
        'metode',
        'referensi',
        'dibayar_pada',
        'dicatat_oleh',
        'akuntansi_jurnal_id',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'dibayar_pada' => 'datetime',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->tagihan_id) {
            return null;
        }

        $sid = Tagihan::withoutGlobalScopes()->whereKey($this->tagihan_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function akuntansiJurnal(): BelongsTo
    {
        return $this->belongsTo(AkuntansiJurnal::class, 'akuntansi_jurnal_id');
    }
}
