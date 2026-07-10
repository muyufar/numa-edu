<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use App\Models\Concerns\HasPresensiKode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    use BelongsToSekolah;
    use HasPresensiKode;

    protected $fillable = [
        'sekolah_id',
        'user_id',
        'presensi_kode',
        'face_descriptor',
        'nip',
        'nama',
        'nik',
        'nuptk',
        'status_kepegawaian',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'nama_ibu_kandung',
        'status_perkawinan',
        'email_pribadi',
        'kewarganegaraan',
        'alamat_jalan',
        'rt_rw',
        'kode_pos',
        'dusun',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'telepon_rumah',
        'jenis_ptk',
        'sk_pengangkatan',
        'tmt_cpns',
        'tmt_pns',
        'tmt_jabatan',
        'tugas',
        'mata_pelajaran',
        'penempatan',
        'total_jtm',
        'phone',
        'jenis_kelamin',
        'kode_provinsi',
        'nama_provinsi',
        'kode_kabupaten',
        'nama_kabupaten',
        'kode_kecamatan',
        'nama_kecamatan',
        'kode_kelurahan',
        'nama_kelurahan',
        'alamat_dusun',
        'foto_path',
        'foto_name',
    ];

    public function fotoUrl(): ?string
    {
        return \App\Support\GtkFoto::url($this->foto_path);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function presensiGurus(): HasMany
    {
        return $this->hasMany(PresensiGuru::class);
    }

    protected function casts(): array
    {
        return [
            'face_descriptor' => 'array',
            'tanggal_lahir' => 'date',
            'tmt_cpns' => 'date',
            'tmt_pns' => 'date',
            'tmt_jabatan' => 'date',
        ];
    }

    protected static function presensiKodePrefix(): string
    {
        return 'GRU';
    }
}
