<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarisMutasi extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const TIPE_OPTIONS = ['in', 'out', 'adjust'];

    protected $fillable = [
        'sekolah_id',
        'inventaris_barang_id',
        'tanggal',
        'tipe',
        'jumlah',
        'referensi',
        'keterangan',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->inventaris_barang_id) {
            return null;
        }

        $sid = InventarisBarang::withoutGlobalScopes()->whereKey($this->inventaris_barang_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(InventarisBarang::class, 'inventaris_barang_id');
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public static function tipeLabel(string $tipe): string
    {
        return match ($tipe) {
            'in' => __('Masuk'),
            'out' => __('Keluar'),
            'adjust' => __('Penyesuaian'),
            default => $tipe,
        };
    }
}
