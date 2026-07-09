<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PerpustakaanBuku extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const TIPE_OPTIONS = ['fisik', 'digital', 'fisik_digital'];

    protected $fillable = [
        'sekolah_id',
        'perpustakaan_kategori_id',
        'judul',
        'pengarang',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'tipe',
        'jumlah_eksemplar',
        'eksemplar_tersedia',
        'rak_lokasi',
        'bahasa',
        'sinopsis',
        'cover_path',
        'cover_name',
        'file_path',
        'file_name',
        'mime',
        'size',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tahun_terbit' => 'integer',
            'jumlah_eksemplar' => 'integer',
            'eksemplar_tersedia' => 'integer',
            'size' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->perpustakaan_kategori_id) {
            return null;
        }

        $sid = PerpustakaanKategori::withoutGlobalScopes()
            ->whereKey($this->perpustakaan_kategori_id)
            ->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(PerpustakaanKategori::class, 'perpustakaan_kategori_id');
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(PerpustakaanPeminjaman::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDigital(Builder $query): Builder
    {
        return $query
            ->whereIn('tipe', ['digital', 'fisik_digital'])
            ->whereNotNull('file_path');
    }

    public function supportsFisik(): bool
    {
        return in_array($this->tipe, ['fisik', 'fisik_digital'], true);
    }

    public function supportsDigital(): bool
    {
        return in_array($this->tipe, ['digital', 'fisik_digital'], true) && $this->file_path;
    }

    public function isPdf(): bool
    {
        if (! $this->file_path) {
            return false;
        }

        if ($this->mime === 'application/pdf') {
            return true;
        }

        return str_ends_with(strtolower((string) $this->file_name), '.pdf');
    }

    public function labelTipe(): string
    {
        return match ($this->tipe) {
            'fisik' => __('Fisik'),
            'digital' => __('Digital'),
            'fisik_digital' => __('Fisik & Digital'),
            default => (string) $this->tipe,
        };
    }

    public function badgeTipeClass(): string
    {
        return match ($this->tipe) {
            'digital' => 'bg-sky-50 text-sky-800 ring-sky-100',
            'fisik_digital' => 'bg-violet-50 text-violet-800 ring-violet-100',
            default => 'bg-amber-50 text-amber-800 ring-amber-100',
        };
    }

    public function userHasAksesDigital(User $user): bool
    {
        return $this->peminjamans()
            ->where('user_id', $user->id)
            ->where('tipe_peminjaman', 'digital')
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_jatuh_tempo', '>=', now()->toDateString())
            ->exists();
    }

    public function hasCover(): bool
    {
        return $this->cover_path !== null
            && Storage::disk('public')->exists($this->cover_path);
    }

    public function coverUrl(): ?string
    {
        if (! $this->hasCover()) {
            return null;
        }

        return '/storage/'.$this->cover_path;
    }
}
