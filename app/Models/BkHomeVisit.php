<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BkHomeVisit extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'siswa_id',
        'tanggal',
        'foto_path',
        'foto_name',
        'catatan_wawancara',
        'hasil_kunjungan',
        'solusi',
        'dilaporkan_kepsek_at',
        'status',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'dilaporkan_kepsek_at' => 'datetime',
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

    public function fotoUrl(): ?string
    {
        if (! $this->foto_path || ! Storage::disk('public')->exists($this->foto_path)) {
            return null;
        }

        return '/storage/'.$this->foto_path;
    }
}
