<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkuntansiAkun extends Model
{
    use BelongsToSekolah;

    /**
     * Kode akun yang dibuat otomatis untuk alur pembayaran & pengeluaran (tidak boleh dihapus).
     *
     * @var list<string>
     */
    public const RESERVED_KODE_SISTEM = ['101', '401', '501'];

    /** @var list<string> */
    public const TIPE_OPTIONS = ['aset', 'kewajiban', 'ekuitas', 'pendapatan', 'beban'];

    public function isReservedSystemKode(): bool
    {
        return in_array($this->kode, self::RESERVED_KODE_SISTEM, true);
    }

    protected $fillable = [
        'sekolah_id',
        'kode',
        'nama',
        'tipe',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function jurnalLines(): HasMany
    {
        return $this->hasMany(AkuntansiJurnalLine::class, 'akun_id');
    }
}

