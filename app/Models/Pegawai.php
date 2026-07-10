<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use App\Models\Concerns\HasPresensiKode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use BelongsToSekolah;
    use HasPresensiKode;

    protected $table = 'pegawais';

    protected $fillable = [
        'sekolah_id',
        'presensi_kode',
        'face_descriptor',
        'nama',
        'nip',
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
        'jabatan',
        'jenis_kelamin',
        'phone',
        'is_active',
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

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'face_descriptor' => 'array',
            'tanggal_lahir' => 'date',
            'tmt_cpns' => 'date',
            'tmt_pns' => 'date',
            'tmt_jabatan' => 'date',
        ];
    }

    protected static function presensiKodePrefix(): string
    {
        return 'PEG';
    }

    public function presensiPegawais(): HasMany
    {
        return $this->hasMany(PresensiPegawai::class);
    }
}
