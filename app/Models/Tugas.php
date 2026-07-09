<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use BelongsToSekolah;

    protected $table = 'tugas';

    /** @var list<string> */
    public const HARI_OPTIONS = Jadwal::HARI_OPTIONS;

    /** @var list<string> */
    public const JENIS_SOAL_OPTIONS = [
        'esai',
        'pilihan_ganda',
    ];

    /** @var list<string> */
    public const TIPE_OPTIONS = [
        'individu',
        'kelompok',
        'latihan',
    ];

    protected $fillable = [
        'sekolah_id',
        'mata_pelajaran_id',
        'kelas_id',
        'guru_id',
        'judul',
        'jenis_soal',
        'bahan_materi',
        'instruksi',
        'hari',
        'jam',
        'tanggal_batas',
        'jam_batas',
        'semester',
        'tahun_ajaran',
        'tipe',
        'bobot',
        'is_published',
        'file_path',
        'file_name',
        'mime',
        'size',
        'diunggah_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_batas' => 'date',
            'bobot' => 'integer',
            'size' => 'integer',
            'is_published' => 'boolean',
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

    public function soals(): HasMany
    {
        return $this->hasMany(TugasSoal::class)->orderBy('urutan');
    }

    public function pengumpulans(): HasMany
    {
        return $this->hasMany(TugasPengumpulan::class);
    }

    public function isPilihanGanda(): bool
    {
        return $this->jenis_soal === 'pilihan_ganda';
    }

    public function isEsai(): bool
    {
        return $this->jenis_soal === 'esai';
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

    public static function tipeLabel(string $tipe): string
    {
        return match ($tipe) {
            'individu' => __('Individu'),
            'kelompok' => __('Kelompok'),
            'latihan' => __('Latihan'),
            default => $tipe,
        };
    }

    public static function jenisSoalLabel(string $jenis): string
    {
        return match ($jenis) {
            'esai' => __('Esai'),
            'pilihan_ganda' => __('Pilihan ganda'),
            default => $jenis,
        };
    }

    public function jadwalLabel(): string
    {
        $parts = array_filter([
            $this->hari,
            $this->jam ? substr((string) $this->jam, 0, 5) : null,
        ]);

        return $parts !== [] ? implode(' · ', $parts) : '—';
    }

    public function batasLabel(): string
    {
        if (! $this->tanggal_batas) {
            return '—';
        }

        $date = $this->tanggal_batas->format('d M Y');
        $time = $this->jam_batas ? substr((string) $this->jam_batas, 0, 5) : null;

        return $time ? "{$date} {$time}" : $date;
    }

    public function isOverdue(): bool
    {
        if (! $this->tanggal_batas) {
            return false;
        }

        $deadline = $this->tanggal_batas->copy();
        if ($this->jam_batas) {
            $time = substr((string) $this->jam_batas, 0, 5);
            [$hour, $minute] = array_pad(explode(':', $time), 2, '0');
            $deadline->setTime((int) $hour, (int) $minute);
        } else {
            $deadline->endOfDay();
        }

        return now()->greaterThan($deadline);
    }
}
