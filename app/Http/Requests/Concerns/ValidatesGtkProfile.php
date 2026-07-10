<?php

namespace App\Http\Requests\Concerns;

trait ValidatesGtkProfile
{
    /**
     * @return array<string, mixed>
     */
    protected function gtkProfileRules(): array
    {
        return [
            'nik' => ['nullable', 'string', 'max:32'],
            'nuptk' => ['nullable', 'string', 'max:32'],
            'status_kepegawaian' => ['nullable', 'string', 'max:64'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'string', 'max:32'],
            'nama_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'status_perkawinan' => ['nullable', 'string', 'max:32'],
            'email_pribadi' => ['nullable', 'string', 'email', 'max:255'],
            'kewarganegaraan' => ['nullable', 'string', 'max:64'],
            'alamat_jalan' => ['nullable', 'string'],
            'rt_rw' => ['nullable', 'string', 'max:16'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'telepon_rumah' => ['nullable', 'string', 'max:20'],
            'jenis_ptk' => ['nullable', 'string', 'max:64'],
            'sk_pengangkatan' => ['nullable', 'string', 'max:255'],
            'tmt_cpns' => ['nullable', 'date'],
            'tmt_pns' => ['nullable', 'date'],
            'tmt_jabatan' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'kode_provinsi' => ['nullable', 'string', 'max:16'],
            'nama_provinsi' => ['nullable', 'string', 'max:191'],
            'kode_kabupaten' => ['nullable', 'string', 'max:24'],
            'nama_kabupaten' => ['nullable', 'string', 'max:191'],
            'kode_kecamatan' => ['nullable', 'string', 'max:24'],
            'nama_kecamatan' => ['nullable', 'string', 'max:191'],
            'kode_kelurahan' => ['nullable', 'string', 'max:24'],
            'nama_kelurahan' => ['nullable', 'string', 'max:191'],
            'alamat_dusun' => ['nullable', 'string'],
        ];
    }
}
