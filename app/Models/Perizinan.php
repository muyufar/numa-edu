<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perizinan extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const JENIS_OPTIONS = ['izin', 'sakit', 'dispensasi'];

    /** @var list<string> */
    public const STATUS_OPTIONS = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'sekolah_id',
        'siswa_id',
        'tanggal',
        'jenis',
        'keterangan',
        'status',
        'diajukan_oleh',
        'ditinjau_oleh',
        'ditinjau_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'ditinjau_pada' => 'datetime',
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

    public function diajukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function ditinjauOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditinjau_oleh');
    }

    public static function jenisLabel(string $jenis): string
    {
        return match ($jenis) {
            'izin' => __('Izin'),
            'sakit' => __('Sakit'),
            'dispensasi' => __('Dispensasi'),
            default => $jenis,
        };
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => __('Menunggu'),
            'approved' => __('Disetujui'),
            'rejected' => __('Ditolak'),
            default => $status,
        };
    }
}
