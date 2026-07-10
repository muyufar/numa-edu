<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

final class GtkProfilePayload
{
    /**
     * @return array<string, mixed>
     */
    public static function wilayahInitial(Model $model): array
    {
        return [
            'kode_provinsi' => old('kode_provinsi', $model->kode_provinsi),
            'nama_provinsi' => old('nama_provinsi', $model->nama_provinsi ?? $model->provinsi),
            'kode_kabupaten' => old('kode_kabupaten', $model->kode_kabupaten),
            'nama_kabupaten' => old('nama_kabupaten', $model->nama_kabupaten ?? $model->kabupaten_kota),
            'kode_kecamatan' => old('kode_kecamatan', $model->kode_kecamatan),
            'nama_kecamatan' => old('nama_kecamatan', $model->nama_kecamatan ?? $model->kecamatan),
            'kode_kelurahan' => old('kode_kelurahan', $model->kode_kelurahan),
            'nama_kelurahan' => old('nama_kelurahan', $model->nama_kelurahan ?? $model->desa_kelurahan),
            'alamat_dusun' => old('alamat_dusun', $model->alamat_dusun ?? $model->dusun),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function mergeWilayahNames(array $data): array
    {
        $data['provinsi'] = self::nullIfEmpty($data['nama_provinsi'] ?? null);
        $data['kabupaten_kota'] = self::nullIfEmpty($data['nama_kabupaten'] ?? null);
        $data['kecamatan'] = self::nullIfEmpty($data['nama_kecamatan'] ?? null);
        $data['desa_kelurahan'] = self::nullIfEmpty($data['nama_kelurahan'] ?? null);
        $data['dusun'] = self::nullIfEmpty($data['alamat_dusun'] ?? null);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function gtkAttributes(array $data): array
    {
        $data = self::mergeWilayahNames($data);

        $keys = [
            'nik', 'nuptk', 'status_kepegawaian', 'tempat_lahir', 'tanggal_lahir',
            'agama', 'nama_ibu_kandung', 'status_perkawinan', 'email_pribadi',
            'kewarganegaraan', 'alamat_jalan', 'rt_rw', 'kode_pos', 'dusun',
            'desa_kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi',
            'telepon_rumah', 'jenis_ptk', 'sk_pengangkatan', 'tmt_cpns',
            'tmt_pns', 'tmt_jabatan', 'jenis_kelamin',
            'kode_provinsi', 'nama_provinsi', 'kode_kabupaten', 'nama_kabupaten',
            'kode_kecamatan', 'nama_kecamatan', 'kode_kelurahan', 'nama_kelurahan',
            'alamat_dusun',
        ];

        return array_intersect_key($data, array_flip($keys));
    }

    private static function nullIfEmpty(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));

        return $v !== '' ? $v : null;
    }
}
