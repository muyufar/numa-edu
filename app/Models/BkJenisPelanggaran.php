<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BkJenisPelanggaran extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'kode',
        'nama',
        'poin',
        'tingkat',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function pelanggarans(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'bk_jenis_pelanggaran_id');
    }
}
