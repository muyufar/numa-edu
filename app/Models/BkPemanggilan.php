<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkPemanggilan extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const TARGET_OPTIONS = ['siswa', 'wali'];

    /** @var list<string> */
    public const STATUS_OPTIONS = ['terjadwal', 'hadir', 'tidak_hadir', 'dijadwal_ulang'];

    protected $fillable = [
        'sekolah_id',
        'siswa_id',
        'target',
        'urutan',
        'tanggal_jadwal',
        'waktu',
        'tempat',
        'alasan',
        'status',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_jadwal' => 'date',
            'urutan' => 'integer',
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

    public static function maxUrutan(string $target): int
    {
        return $target === 'wali' ? 2 : 3;
    }

    public static function targetLabel(string $target): string
    {
        return match ($target) {
            'wali' => __('Wali murid'),
            default => __('Siswa'),
        };
    }
}
