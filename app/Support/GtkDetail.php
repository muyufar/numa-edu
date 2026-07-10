<?php

namespace App\Support;

use App\Models\Guru;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

final class GtkDetail
{
    public function __construct(
        public readonly string $type,
        public readonly Model $model,
        public readonly string $nama,
        public readonly ?string $nip,
        public readonly ?string $nuptk,
        public readonly ?string $nik,
        public readonly ?string $statusKepegawaian,
        public readonly ?string $jenisKelamin,
        public readonly ?string $tempatLahir,
        public readonly ?string $tanggalLahir,
        public readonly ?string $agama,
        public readonly ?string $namaIbuKandung,
        public readonly ?string $statusPerkawinan,
        public readonly ?string $email,
        public readonly ?string $emailLogin,
        public readonly ?string $kewarganegaraan,
        public readonly ?string $alamatJalan,
        public readonly ?string $rtRw,
        public readonly ?string $kodePos,
        public readonly ?string $dusun,
        public readonly ?string $desaKelurahan,
        public readonly ?string $kecamatan,
        public readonly ?string $kabupatenKota,
        public readonly ?string $provinsi,
        public readonly ?string $teleponRumah,
        public readonly ?string $noHp,
        public readonly ?string $jabatan,
        public readonly ?string $jenisPtk,
        public readonly ?string $skPengangkatan,
        public readonly ?string $tmtCpns,
        public readonly ?string $tmtPns,
        public readonly ?string $tmtJabatan,
        public readonly ?string $tugas,
        public readonly ?string $mataPelajaran,
        public readonly ?string $penempatan,
        public readonly ?string $totalJtm,
        public readonly ?string $presensiKode,
        public readonly bool $isActive,
        public readonly ?string $fotoUrl,
    ) {}

    public static function fromGuru(Guru $guru): self
    {
        $guru->loadMissing('user:id,email');

        return new self(
            type: 'guru',
            model: $guru,
            nama: $guru->nama,
            nip: $guru->nip,
            nuptk: $guru->nuptk,
            nik: $guru->nik,
            statusKepegawaian: $guru->status_kepegawaian,
            jenisKelamin: self::formatJenisKelamin($guru->jenis_kelamin),
            tempatLahir: $guru->tempat_lahir,
            tanggalLahir: self::formatDate($guru->tanggal_lahir),
            agama: $guru->agama,
            namaIbuKandung: $guru->nama_ibu_kandung,
            statusPerkawinan: $guru->status_perkawinan,
            email: $guru->email_pribadi,
            emailLogin: $guru->user?->email,
            kewarganegaraan: $guru->kewarganegaraan,
            alamatJalan: $guru->alamat_jalan,
            rtRw: $guru->rt_rw,
            kodePos: $guru->kode_pos,
            dusun: $guru->dusun,
            desaKelurahan: $guru->desa_kelurahan,
            kecamatan: $guru->kecamatan,
            kabupatenKota: $guru->kabupaten_kota,
            provinsi: $guru->provinsi,
            teleponRumah: $guru->telepon_rumah,
            noHp: $guru->phone,
            jabatan: $guru->tugas,
            jenisPtk: $guru->jenis_ptk,
            skPengangkatan: $guru->sk_pengangkatan,
            tmtCpns: self::formatDate($guru->tmt_cpns),
            tmtPns: self::formatDate($guru->tmt_pns),
            tmtJabatan: self::formatDate($guru->tmt_jabatan),
            tugas: $guru->tugas,
            mataPelajaran: $guru->mata_pelajaran,
            penempatan: $guru->penempatan,
            totalJtm: $guru->total_jtm,
            presensiKode: $guru->presensi_kode,
            isActive: true,
            fotoUrl: $guru->fotoUrl(),
        );
    }

    public static function fromPegawai(Pegawai $pegawai): self
    {
        return new self(
            type: 'pegawai',
            model: $pegawai,
            nama: $pegawai->nama,
            nip: $pegawai->nip,
            nuptk: $pegawai->nuptk,
            nik: $pegawai->nik,
            statusKepegawaian: $pegawai->status_kepegawaian,
            jenisKelamin: self::formatJenisKelamin($pegawai->jenis_kelamin),
            tempatLahir: $pegawai->tempat_lahir,
            tanggalLahir: self::formatDate($pegawai->tanggal_lahir),
            agama: $pegawai->agama,
            namaIbuKandung: $pegawai->nama_ibu_kandung,
            statusPerkawinan: $pegawai->status_perkawinan,
            email: $pegawai->email_pribadi,
            emailLogin: null,
            kewarganegaraan: $pegawai->kewarganegaraan,
            alamatJalan: $pegawai->alamat_jalan,
            rtRw: $pegawai->rt_rw,
            kodePos: $pegawai->kode_pos,
            dusun: $pegawai->dusun,
            desaKelurahan: $pegawai->desa_kelurahan,
            kecamatan: $pegawai->kecamatan,
            kabupatenKota: $pegawai->kabupaten_kota,
            provinsi: $pegawai->provinsi,
            teleponRumah: $pegawai->telepon_rumah,
            noHp: $pegawai->phone,
            jabatan: $pegawai->jabatan,
            jenisPtk: $pegawai->jenis_ptk,
            skPengangkatan: $pegawai->sk_pengangkatan,
            tmtCpns: self::formatDate($pegawai->tmt_cpns),
            tmtPns: self::formatDate($pegawai->tmt_pns),
            tmtJabatan: self::formatDate($pegawai->tmt_jabatan),
            tugas: $pegawai->jabatan,
            mataPelajaran: null,
            penempatan: null,
            totalJtm: null,
            presensiKode: $pegawai->presensi_kode,
            isActive: (bool) $pegawai->is_active,
            fotoUrl: $pegawai->fotoUrl(),
        );
    }

    public function tab(): string
    {
        return $this->type === 'guru' ? 'guru' : 'pegawai';
    }

    public function typeLabel(): string
    {
        return $this->type === 'guru' ? __('Guru') : __('Tenaga Kependidikan');
    }

    public function display(?string $value): string
    {
        $v = trim((string) ($value ?? ''));

        return $v !== '' ? $v : '—';
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    public function tugasKependidikanRows(): array
    {
        if ($this->tugas === null && $this->mataPelajaran === null && $this->penempatan === null && $this->totalJtm === null) {
            return [];
        }

        return [[
            $this->display($this->tugas),
            $this->display($this->mataPelajaran),
            $this->typeLabel(),
            $this->display($this->totalJtm),
        ]];
    }

    private static function formatJenisKelamin(?string $value): ?string
    {
        return match ($value) {
            'L' => __('Laki-laki'),
            'P' => __('Perempuan'),
            default => $value,
        };
    }

    private static function formatDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('d-m-Y');
        } catch (\Throwable) {
            return is_string($value) ? $value : null;
        }
    }
}
