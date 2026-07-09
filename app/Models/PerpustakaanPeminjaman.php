<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerpustakaanPeminjaman extends Model
{
    use BelongsToSekolah;

    protected $table = 'perpustakaan_peminjamans';

    /** @var list<string> */
    public const TIPE_OPTIONS = ['fisik', 'digital'];

    /** @var list<string> */
    public const STATUS_OPTIONS = ['dipinjam', 'dikembalikan', 'terlambat', 'hilang'];

    protected $fillable = [
        'sekolah_id',
        'perpustakaan_buku_id',
        'user_id',
        'siswa_id',
        'guru_id',
        'tipe_peminjaman',
        'status',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'tanggal_kembali',
        'jumlah_perpanjangan',
        'denda',
        'catatan',
        'diproses_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_jatuh_tempo' => 'date',
            'tanggal_kembali' => 'date',
            'jumlah_perpanjangan' => 'integer',
            'denda' => 'integer',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->perpustakaan_buku_id) {
            return null;
        }

        $sid = PerpustakaanBuku::withoutGlobalScopes()
            ->whereKey($this->perpustakaan_buku_id)
            ->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(PerpustakaanBuku::class, 'perpustakaan_buku_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'dipinjam');
    }

    public function scopeUntukUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function isAktif(): bool
    {
        return $this->status === 'dipinjam';
    }

    public function isTerlambat(): bool
    {
        return $this->isAktif() && $this->tanggal_jatuh_tempo->isPast();
    }

    public function labelStatus(): string
    {
        if ($this->isTerlambat()) {
            return __('Terlambat');
        }

        return match ($this->status) {
            'dipinjam' => __('Dipinjam'),
            'dikembalikan' => __('Dikembalikan'),
            'terlambat' => __('Terlambat'),
            'hilang' => __('Hilang'),
            default => (string) $this->status,
        };
    }

    public function badgeStatusClass(): string
    {
        if ($this->isTerlambat()) {
            return 'bg-red-50 text-red-800 ring-red-100';
        }

        return match ($this->status) {
            'dipinjam' => 'bg-sky-50 text-sky-800 ring-sky-100',
            'dikembalikan' => 'bg-emerald-50 text-emerald-800 ring-emerald-100',
            'hilang' => 'bg-gray-100 text-gray-700 ring-gray-200',
            default => 'bg-amber-50 text-amber-800 ring-amber-100',
        };
    }

    public function namaPeminjam(): string
    {
        return $this->siswa?->nama
            ?? $this->guru?->nama
            ?? $this->user?->name
            ?? '—';
    }
}
