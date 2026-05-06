<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const STATUS_OPTIONS = ['unpaid', 'partial', 'paid'];

    protected $fillable = [
        'sekolah_id',
        'siswa_id',
        'jenis',
        'periode',
        'jumlah',
        'jatuh_tempo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'jatuh_tempo' => 'date',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->siswa_id) {
            return null;
        }

        $sid = Siswa::withoutGlobalScopes()->whereKey($this->siswa_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function totalDibayar(): float
    {
        return (float) $this->pembayarans()->sum('jumlah');
    }

    public function sisa(): float
    {
        return max(0, (float) $this->jumlah - $this->totalDibayar());
    }

    public function refreshStatus(): void
    {
        $total = $this->totalDibayar();
        $due = (float) $this->jumlah;

        if ($due <= 0.00001) {
            $status = 'paid';
        } elseif ($total <= 0.00001) {
            $status = 'unpaid';
        } elseif ($total + 0.00001 >= $due) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }
}
