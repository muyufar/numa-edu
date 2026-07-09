<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MateriAjar extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const JENIS_OPTIONS = [
        'modul',
        'rpp',
        'modul_pembelajaran',
        'silabus',
        'lkpd',
        'bahan_ajar',
        'media',
        'lainnya',
    ];

    /** @var list<string> */
    public const STATUS_PUBLIKASI_OPTIONS = [
        'draft',
        'dipublikasi',
        'diarsipkan',
    ];

    /** @var list<string> */
    public const STATUS_PENGGUNAAN_OPTIONS = [
        'rencana',
        'aktif',
        'selesai',
    ];

    protected $fillable = [
        'sekolah_id',
        'mata_pelajaran_id',
        'kelas_id',
        'guru_id',
        'judul',
        'jenis',
        'fase',
        'elemen_topik',
        'alokasi_waktu',
        'model_pembelajaran',
        'deskripsi',
        'konten_modul',
        'status_publikasi',
        'status_penggunaan',
        'pertemuan_ke',
        'semester',
        'tahun_ajaran',
        'tanggal',
        'file_path',
        'file_name',
        'mime',
        'size',
        'diunggah_oleh',
        'dipublikasi_pada',
        'diarsipkan_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'size' => 'integer',
            'pertemuan_ke' => 'integer',
            'konten_modul' => 'array',
            'dipublikasi_pada' => 'datetime',
            'diarsipkan_pada' => 'datetime',
        ];
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function diunggahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }

    public function scopeDipublikasi(Builder $query): Builder
    {
        return $query->where('status_publikasi', 'dipublikasi');
    }

    public function scopeUntukSiswaWali(Builder $query): Builder
    {
        return $query->dipublikasi();
    }

    public function isDraft(): bool
    {
        return $this->status_publikasi === 'draft';
    }

    public function isDipublikasi(): bool
    {
        return $this->status_publikasi === 'dipublikasi';
    }

    public function isDiarsipkan(): bool
    {
        return $this->status_publikasi === 'diarsipkan';
    }

    public function labelJenis(): string
    {
        return match ($this->jenis) {
            'modul' => __('Modul ajar'),
            'rpp' => __('RPP'),
            'modul_pembelajaran' => __('Modul pembelajaran'),
            'silabus' => __('Silabus'),
            'lkpd' => __('LKPD'),
            'bahan_ajar' => __('Bahan ajar'),
            'media' => __('Media pembelajaran'),
            default => __('Lainnya'),
        };
    }

    public function labelStatusPublikasi(): string
    {
        return match ($this->status_publikasi) {
            'draft' => __('Draft'),
            'dipublikasi' => __('Dipublikasi'),
            'diarsipkan' => __('Diarsipkan'),
            default => (string) $this->status_publikasi,
        };
    }

    public function labelStatusPenggunaan(): string
    {
        return match ($this->status_penggunaan) {
            'rencana' => __('Akan digunakan'),
            'aktif' => __('Sedang digunakan'),
            'selesai' => __('Sudah digunakan'),
            default => (string) $this->status_penggunaan,
        };
    }

    public function publish(): void
    {
        $this->update([
            'status_publikasi' => 'dipublikasi',
            'dipublikasi_pada' => now(),
            'diarsipkan_pada' => null,
        ]);
    }

    public function archive(): void
    {
        $this->update([
            'status_publikasi' => 'diarsipkan',
            'status_penggunaan' => 'selesai',
            'diarsipkan_pada' => now(),
        ]);
    }

    public function isPdf(): bool
    {
        if (! $this->file_path) {
            return false;
        }

        if ($this->mime === 'application/pdf') {
            return true;
        }

        $name = strtolower((string) $this->file_name);

        return str_ends_with($name, '.pdf');
    }

    public function isModulMerdeka(): bool
    {
        return $this->jenis === 'modul';
    }

    public function isRpp(): bool
    {
        return $this->jenis === 'rpp';
    }

    public function isModulPembelajaran(): bool
    {
        return $this->jenis === 'modul_pembelajaran';
    }

    public function isLkpd(): bool
    {
        return $this->jenis === 'lkpd';
    }

    public function lkpdSistematika(): string
    {
        return \App\Support\LkpdSistematika::resolveAlternatif($this->konten_modul);
    }

    public function hasKontenTerstruktur(): bool
    {
        return \App\Support\PerangkatAjarJenis::supportsKontenDigital($this->jenis);
    }

    /**
     * @return array<string, string>
     */
    public function kontenModulNormalized(): array
    {
        return \App\Support\PerangkatAjarJenis::normalizeKonten($this->jenis, $this->konten_modul);
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if ($this->kelas_id) {
            $sid = Kelas::withoutGlobalScopes()->whereKey($this->kelas_id)->value('sekolah_id');

            return $sid !== null ? (int) $sid : null;
        }

        if ($this->mata_pelajaran_id) {
            $sid = MataPelajaran::withoutGlobalScopes()->whereKey($this->mata_pelajaran_id)->value('sekolah_id');

            return $sid !== null ? (int) $sid : null;
        }

        return null;
    }
}
