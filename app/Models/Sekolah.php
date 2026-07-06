<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    /** @var list<string> */
    public const JENJANG_KEYS = ['mi', 'sd', 'mts', 'smp', 'ma', 'sma', 'smk', 'slb'];

    protected $fillable = [
        'cabang_id',
        'npsn',
        'nama',
        'jenjang',
        'alamat',
        'kode_provinsi',
        'nama_provinsi',
        'kode_kabupaten',
        'nama_kabupaten',
        'kode_kecamatan',
        'nama_kecamatan',
        'kode_kelurahan',
        'nama_kelurahan',
        'alamat_dusun',
        'telepon',
        'email_kantor',
        'website',
        'npwp',
        'medsos',
        'tahun_berdiri',
        'waktu_belajar',
        'status_kkm',
        'komite',
        'rt',
        'rw',
        'kodepos',
        'kepala_nama',
        'kepala_nip',
        'akreditasi',
        'akreditasi_tahun',
        'is_active',
        'presensi_siswa_mode',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'tahun_berdiri' => 'integer',
        ];
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return array<string, string>
     */
    public static function jenjangOptions(): array
    {
        return [
            'mi' => __('MI (Madrasah Ibtidaiyah)'),
            'sd' => __('SD'),
            'mts' => __('MTs (Madrasah Tsanawiyah)'),
            'smp' => __('SMP'),
            'ma' => __('MA (Madrasah Aliyah)'),
            'sma' => __('SMA'),
            'smk' => __('SMK'),
            'slb' => __('SLB'),
        ];
    }

    public static function jenjangLabel(?string $key): string
    {
        if ($key === null || $key === '') {
            return '';
        }

        return self::jenjangOptions()[$key] ?? $key;
    }

    /**
     * Ringkasan nama wilayah untuk tampilan (tanpa dusun).
     */
    public function alamatWilayahRingkas(): string
    {
        return collect([
            $this->nama_kelurahan,
            $this->nama_kecamatan,
            $this->nama_kabupaten,
            $this->nama_provinsi,
        ])->filter()->implode(', ');
    }
}
