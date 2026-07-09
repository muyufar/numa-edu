<?php

namespace App\Support;

final class RppMerdeka
{
    /**
     * Kolom RPP — berfokus pada perencanaan guru & langkah pembelajaran di kelas.
     *
     * @return array<string, array{label: string, group: string, rows: int, placeholder?: string}>
     */
    public static function kontenFields(): array
    {
        return [
            'capaian_pembelajaran' => [
                'label' => 'Capaian Pembelajaran (CP)',
                'group' => 'perencanaan',
                'rows' => 4,
                'placeholder' => 'Peserta didik dapat ...',
            ],
            'tujuan_pembelajaran' => [
                'label' => 'Tujuan Pembelajaran (TP)',
                'group' => 'perencanaan',
                'rows' => 4,
                'placeholder' => 'Setelah pembelajaran, peserta didik dapat ...',
            ],
            'profil_pelajar_pancasila' => [
                'label' => 'Profil Pelajar Pancasila',
                'group' => 'perencanaan',
                'rows' => 4,
            ],
            'praktik_pedagogis' => [
                'label' => 'Praktik Pedagogis',
                'group' => 'perencanaan',
                'rows' => 3,
                'placeholder' => 'Berbagi, bermain peran, diskusi, ...',
            ],
            'kemitraan_pembelajaran' => [
                'label' => 'Kemitraan Pembelajaran',
                'group' => 'perencanaan',
                'rows' => 3,
            ],
            'lingkungan_pembelajaran' => [
                'label' => 'Lingkungan Pembelajaran',
                'group' => 'perencanaan',
                'rows' => 3,
            ],
            'kegiatan_pendahuluan' => [
                'label' => 'Kegiatan Pendahuluan',
                'group' => 'langkah_pembelajaran',
                'rows' => 6,
                'placeholder' => 'Apersepsi, motivasi, penyampaian tujuan, ...',
            ],
            'kegiatan_inti' => [
                'label' => 'Kegiatan Inti',
                'group' => 'langkah_pembelajaran',
                'rows' => 12,
                'placeholder' => 'Langkah-langkah guru memfasilitasi pembelajaran di kelas...',
            ],
            'kegiatan_penutup' => [
                'label' => 'Kegiatan Penutup',
                'group' => 'langkah_pembelajaran',
                'rows' => 5,
                'placeholder' => 'Refleksi, kesimpulan, tindak lanjut, ...',
            ],
            'rencana_asesmen' => [
                'label' => 'Rencana Asesmen',
                'group' => 'penilaian',
                'rows' => 5,
                'placeholder' => 'Diagnostik, formatif, sumatif — teknik & instrumen',
            ],
            'teknik_penilaian' => [
                'label' => 'Teknik & Instrumen Penilaian',
                'group' => 'penilaian',
                'rows' => 5,
            ],
            'rubrik_penilaian' => [
                'label' => 'Rubrik / Kriteria Penilaian',
                'group' => 'penilaian',
                'rows' => 5,
            ],
            'media_sumber_belajar' => [
                'label' => 'Media & Sumber Belajar',
                'group' => 'pendukung',
                'rows' => 4,
            ],
            'refleksi_guru' => [
                'label' => 'Refleksi Guru',
                'group' => 'pendukung',
                'rows' => 4,
                'placeholder' => 'Apa yang berjalan baik, apa yang perlu diperbaiki?',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function groupLabels(): array
    {
        return [
            'perencanaan' => 'I. Perencanaan Pembelajaran',
            'langkah_pembelajaran' => 'II. Langkah-langkah Pembelajaran',
            'penilaian' => 'III. Penilaian (Hasil Belajar)',
            'pendukung' => 'IV. Media, Sumber & Refleksi',
        ];
    }
}
