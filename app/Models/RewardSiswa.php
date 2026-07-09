<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardSiswa extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const KATEGORI_OPTIONS = ['nilai', 'administrasi'];

    protected $fillable = [
        'sekolah_id',
        'siswa_id',
        'kategori',
        'judul',
        'poin',
        'tanggal',
        'keterangan',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'poin' => 'integer',
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

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public static function kategoriLabel(string $kategori): string
    {
        return match ($kategori) {
            'nilai' => __('Nilai / prestasi'),
            'administrasi' => __('Administrasi / kehadiran'),
            default => $kategori,
        };
    }
}
