<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemasukanKas extends Model
{
    use BelongsToSekolah;

    protected $table = 'pemasukan_kass';

    protected $fillable = [
        'sekolah_id',
        'tanggal',
        'jumlah',
        'keterangan',
        'no_bukti',
        'bukti_nota_path',
        'akun_pendapatan_id',
        'akuntansi_jurnal_id',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'decimal:2',
        ];
    }

    public function akunPendapatan(): BelongsTo
    {
        return $this->belongsTo(AkuntansiAkun::class, 'akun_pendapatan_id');
    }

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(AkuntansiJurnal::class, 'akuntansi_jurnal_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
