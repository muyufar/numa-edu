<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LembagaRegistration extends Model
{
    public const STATUS_AWAITING_MOU = 'awaiting_mou';

    public const STATUS_PENDING_REVIEW = 'pending_review';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /** @return list<array{key: string, label: string}> */
    public static function permitDefinitions(): array
    {
        return [
            [
                'key' => 'akta_pendirian_penyelenggara',
                'label' => 'AKTA PENDIRIAN PENYELENGGARA',
            ],
            [
                'key' => 'sk_kemenkumham',
                'label' => 'SK KEMENKUMHAM',
            ],
            [
                'key' => 'sk_izin_operasional_sebelum_1385',
                'label' => 'SK IZIN OPERASIONAL SEBELUM PENERBITAN SK DIRJEN PENDIS NO. 1385 TH 2015',
            ],
            [
                'key' => 'sk_izin_operasional',
                'label' => 'SK IZIN OPERASIONAL',
            ],
        ];
    }

    /** @return list<array{key: string, label: string}> */
    public static function fotoGaleriDefinitions(): array
    {
        return [
            ['key' => 'papan_nama', 'label' => 'Foto Papan Nama'],
            ['key' => 'gedung_depan', 'label' => 'Foto Gedung (Tampak Depan)'],
            ['key' => 'kelas', 'label' => 'Foto Kelas'],
            ['key' => 'halaman', 'label' => 'Foto Halaman'],
        ];
    }

    protected $fillable = [
        'public_token',
        'status',
        'cabang_id',
        'sekolah_id',
        'npsn',
        'nama_lembaga',
        'nama_kepala',
        'jenjang',
        'npwp',
        'telepon',
        'website',
        'email',
        'medsos',
        'tahun_berdiri',
        'waktu_belajar',
        'status_kkm',
        'komite',
        'jumlah_murid',
        'alamat_jalan',
        'rt',
        'rw',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kodepos',
        'foto_papan_nama_path',
        'foto_gedung_path',
        'foto_kelas_path',
        'foto_halaman_path',
        'foto_denah_path',
        'operator_name',
        'operator_email',
        'mou_nomor_lp',
        'mou_nomor_sekolah',
        'mou_signed_at',
        'signature_path',
        'materai_path',
        'mou_draft_pdf_path',
        'e_sertifikat_pdf_path',
        'admin_notes',
        'rejected_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_murid' => 'integer',
            'tahun_berdiri' => 'integer',
            'mou_signed_at' => 'datetime',
            'rejected_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function permits(): HasMany
    {
        return $this->hasMany(LembagaRegistrationPermit::class);
    }

    public function alamatLengkap(): string
    {
        $parts = array_filter([
            $this->alamat_jalan,
            trim(implode(' ', array_filter([$this->rt ? 'RT '.$this->rt : null, $this->rw ? 'RW '.$this->rw : null]))),
            $this->desa_kelurahan,
            $this->kecamatan,
            $this->kabupaten_kota,
            $this->provinsi,
            $this->kodepos ? 'Kode pos '.$this->kodepos : null,
        ]);

        return implode(', ', $parts);
    }

    public function needsMou(): bool
    {
        return $this->status === self::STATUS_AWAITING_MOU;
    }

    public function isPendingReview(): bool
    {
        return $this->status === self::STATUS_PENDING_REVIEW;
    }
}
