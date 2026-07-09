<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use App\Models\Concerns\HasPresensiKode;
use App\Services\SiswaAkunService;
use App\Support\SiswaAkunEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use BelongsToSekolah;
    use HasPresensiKode;

    protected $fillable = [
        'sekolah_id',
        'user_id',
        'ppdb_registration_id',
        'kelas_id',
        'presensi_kode',
        'face_descriptor',
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
        'foto_siswa_path',
        'foto_siswa_name',
        'dok_ijazah_path',
        'dok_ijazah_name',
        'dok_kk_path',
        'dok_kk_name',
        'dok_ktp_ortu_path',
        'dok_ktp_ortu_name',
        'dok_kip_path',
        'dok_kip_name',
        'dok_kia_path',
        'dok_kia_name',
        'dok_akta_path',
        'dok_akta_name',
        'dok_piagam_path',
        'dok_piagam_name',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'face_descriptor' => 'array',
        ];
    }

    protected static function presensiKodePrefix(): string
    {
        return 'SIS';
    }

    protected static function booted(): void
    {
        static::saved(function (Siswa $siswa): void {
            if ($siswa->user_id || ! $siswa->nisn) {
                return;
            }

            app(SiswaAkunService::class)->provision($siswa);
        });
    }

    public function suggestedAkunEmail(): ?string
    {
        return SiswaAkunEmail::fromNisn($this->nisn);
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

    public function scopeAlumni($query)
    {
        return $query->where(function ($q) {
            $q->whereRaw('LOWER(TRIM(COALESCE(status, ""))) IN (?, ?, ?)', ['alumni', 'lulus', 'tamat'])
                ->orWhereRaw('LOWER(TRIM(COALESCE(status, ""))) LIKE ?', ['%alumni%']);
        });
    }

    public function scopeBukanAlumni($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('status')
                ->orWhereRaw('TRIM(COALESCE(status, "")) = ?', [''])
                ->orWhere(function ($inner) {
                    $inner->whereRaw('LOWER(TRIM(status)) NOT IN (?, ?, ?)', ['alumni', 'lulus', 'tamat'])
                        ->whereRaw('LOWER(TRIM(status)) NOT LIKE ?', ['%alumni%']);
                });
        });
    }

    public function isAlumni(): bool
    {
        $status = strtolower(trim((string) $this->status));

        if ($status === '') {
            return false;
        }

        return in_array($status, ['alumni', 'lulus', 'tamat'], true) || str_contains($status, 'alumni');
    }
}
