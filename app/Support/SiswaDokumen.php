<?php

namespace App\Support;

final class SiswaDokumen
{
    /** @return array<string, array{path: string, name: string, label: string, accept: string}> */
    public static function fields(): array
    {
        return [
            'foto_siswa' => [
                'path' => 'foto_siswa_path',
                'name' => 'foto_siswa_name',
                'label' => 'Foto siswa',
                'accept' => 'image/*',
            ],
            'ijazah' => [
                'path' => 'dok_ijazah_path',
                'name' => 'dok_ijazah_name',
                'label' => 'Ijazah',
                'accept' => 'image/*,.pdf',
            ],
            'kk' => [
                'path' => 'dok_kk_path',
                'name' => 'dok_kk_name',
                'label' => 'Kartu Keluarga (KK)',
                'accept' => 'image/*,.pdf',
            ],
            'ktp_ortu' => [
                'path' => 'dok_ktp_ortu_path',
                'name' => 'dok_ktp_ortu_name',
                'label' => 'KTP orang tua',
                'accept' => 'image/*,.pdf',
            ],
            'kip' => [
                'path' => 'dok_kip_path',
                'name' => 'dok_kip_name',
                'label' => 'KIP',
                'accept' => 'image/*,.pdf',
            ],
            'kia' => [
                'path' => 'dok_kia_path',
                'name' => 'dok_kia_name',
                'label' => 'KIA',
                'accept' => 'image/*,.pdf',
            ],
            'akta' => [
                'path' => 'dok_akta_path',
                'name' => 'dok_akta_name',
                'label' => 'Akta kelahiran',
                'accept' => 'image/*,.pdf',
            ],
            'piagam' => [
                'path' => 'dok_piagam_path',
                'name' => 'dok_piagam_name',
                'label' => 'Piagam',
                'accept' => 'image/*,.pdf',
            ],
        ];
    }
}
