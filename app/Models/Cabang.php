<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    protected $fillable = [
        'nama',
        'kode',
        'mou_lp_next_sequence',
        'mou_lp_number_digits',
        'mou_lp_number_suffix',
        'mou_penandatangan_nama',
        'mou_penandatangan_jabatan',
        'mou_surat_kota',
        'mou_stempel_path',
        'mou_penandatangan_ttd_path',
    ];

    protected function casts(): array
    {
        return [
            'mou_lp_next_sequence' => 'integer',
            'mou_lp_number_digits' => 'integer',
        ];
    }

    public function sekolahs(): HasMany
    {
        return $this->hasMany(Sekolah::class);
    }
}
