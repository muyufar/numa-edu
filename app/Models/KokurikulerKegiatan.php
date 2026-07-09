<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class KokurikulerKegiatan extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'kelas_id',
        'judul',
        'tempat',
        'tanggal',
        'laporan',
        'lkpd_path',
        'lkpd_name',
        'status',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function anggotas(): HasMany
    {
        return $this->hasMany(KokurikulerAnggota::class);
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function lkpdUrl(): ?string
    {
        if (! $this->lkpd_path || ! Storage::disk('public')->exists($this->lkpd_path)) {
            return null;
        }

        return '/storage/'.$this->lkpd_path;
    }
}
