<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'user_id',
        'ppdb_registration_id',
        'kelas_id',
        'nis',
        'nisn',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'tingkat_rombel',
        'umur',
        'status',
        'jenis_kelamin',
        'alamat',
        'no_telepon',
        'kebutuhan_khusus',
        'disabilitas',
        'nomor_kip_pip',
        'nama_ayah_kandung',
        'nama_ibu_kandung',
        'nama_wali',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->kelas_id) {
            return null;
        }

        $sid = Kelas::withoutGlobalScopes()->whereKey($this->kelas_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function ppdbRegistration(): BelongsTo
    {
        return $this->belongsTo(PpdbRegistration::class);
    }

    public function walis(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wali_siswa')
            ->withPivot('hubungan')
            ->withTimestamps();
    }

    public function presensiSiswas(): HasMany
    {
        return $this->hasMany(PresensiSiswa::class);
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pelanggarans(): HasMany
    {
        return $this->hasMany(Pelanggaran::class);
    }

    public function perizinans(): HasMany
    {
        return $this->hasMany(Perizinan::class);
    }
}
