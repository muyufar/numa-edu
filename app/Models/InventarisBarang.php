<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventarisBarang extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const KONDISI_OPTIONS = [
        'normal',
        'rusak',
        'perbaikan',
    ];

    protected $fillable = [
        'sekolah_id',
        'inventaris_kategori_id',
        'nama',
        'kode',
        'satuan',
        'stok_awal',
        'stok_minimum',
        'is_active',
        'kondisi',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->inventaris_kategori_id) {
            return null;
        }

        $sid = InventarisKategori::withoutGlobalScopes()->whereKey($this->inventaris_kategori_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(InventarisKategori::class, 'inventaris_kategori_id');
    }

    public function mutasis(): HasMany
    {
        return $this->hasMany(InventarisMutasi::class);
    }

    public function getStokAkhirAttribute(): int
    {
        $in = (int) $this->mutasis()->where('tipe', 'in')->sum('jumlah');
        $out = (int) $this->mutasis()->where('tipe', 'out')->sum('jumlah');
        $adjust = (int) $this->mutasis()->where('tipe', 'adjust')->sum('jumlah');

        return (int) $this->stok_awal + $in - $out + $adjust;
    }

    public static function kondisiLabel(string $kondisi): string
    {
        return match ($kondisi) {
            'normal' => __('Normal'),
            'rusak' => __('Rusak'),
            'perbaikan' => __('Perbaikan'),
            default => $kondisi,
        };
    }

    public static function kondisiBadgeClass(string $kondisi): string
    {
        return match ($kondisi) {
            'rusak' => 'bg-red-50 text-red-800 ring-red-200',
            'perbaikan' => 'bg-amber-50 text-amber-900 ring-amber-200',
            default => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        };
    }
}
