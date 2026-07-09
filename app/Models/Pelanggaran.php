<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelanggaran extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const JENIS_KEYS = [
        'terlambat',
        'seragam',
        'atribut',
        'kelakuan',
        'perundungan',
        'hp',
        'tugas',
        'lainnya',
    ];

    protected $fillable = [
        'sekolah_id',
        'siswa_id',
        'tanggal',
        'jenis',
        'bk_jenis_pelanggaran_id',
        'bk_sanksi_id',
        'poin',
        'tingkat',
        'deskripsi',
        'tindakan',
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

    public function bkJenis(): BelongsTo
    {
        return $this->belongsTo(BkJenisPelanggaran::class, 'bk_jenis_pelanggaran_id');
    }

    public function bkSanksi(): BelongsTo
    {
        return $this->belongsTo(BkSanksi::class, 'bk_sanksi_id');
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public static function jenisLabel(string $jenis): string
    {
        return match ($jenis) {
            'terlambat' => __('Keterlambatan'),
            'seragam' => __('Pelanggaran seragam'),
            'atribut' => __('Atribut / kelengkapan'),
            'kelakuan' => __('Kelakuan di kelas'),
            'perundungan' => __('Perundungan / konflik'),
            'hp' => __('Penggunaan ponsel'),
            'tugas' => __('Ketidakhadiran tugas'),
            'lainnya' => __('Lainnya'),
            default => $jenis,
        };
    }
}
