<?php

namespace App\Support;

final class ModulPembelajaran
{
    /**
     * Kolom Modul Pembelajaran — bahan ajar mandiri untuk siswa.
     *
     * @return array<string, array{label: string, group: string, rows: int, placeholder?: string, guru_only?: bool}>
     */
    public static function kontenFields(): array
    {
        return [
            'kompetensi_dasar' => [
                'label' => 'Kompetensi / Capaian yang Dicapai',
                'group' => 'materi_tujuan',
                'rows' => 4,
            ],
            'tujuan_pembelajaran' => [
                'label' => 'Tujuan Pembelajaran',
                'group' => 'materi_tujuan',
                'rows' => 4,
                'placeholder' => 'Setelah mempelajari modul ini, peserta didik dapat ...',
            ],
            'materi_pembelajaran' => [
                'label' => 'Materi Pembelajaran',
                'group' => 'materi_tujuan',
                'rows' => 14,
                'placeholder' => 'Uraian materi ajar lengkap untuk dibaca siswa secara mandiri...',
            ],
            'aktivitas_belajar_mandiri' => [
                'label' => 'Aktivitas Belajar Mandiri',
                'group' => 'materi_tujuan',
                'rows' => 6,
                'placeholder' => 'Petunjuk kerja, eksperimen sederhana, proyek mini, ...',
            ],
            'latihan_soal' => [
                'label' => 'Latihan Soal',
                'group' => 'latihan_evaluasi',
                'rows' => 10,
                'placeholder' => "1. ...\n2. ...\n3. ...",
            ],
            'evaluasi_mandiri' => [
                'label' => 'Evaluasi Mandiri',
                'group' => 'latihan_evaluasi',
                'rows' => 6,
                'placeholder' => 'Tes singkat atau refleksi diri untuk siswa',
            ],
            'kunci_jawaban' => [
                'label' => 'Kunci Jawaban (untuk guru)',
                'group' => 'latihan_evaluasi',
                'rows' => 6,
                'guru_only' => true,
            ],
            'referensi' => [
                'label' => 'Referensi & Daftar Pustaka',
                'group' => 'referensi',
                'rows' => 4,
            ],
            'glosarium' => [
                'label' => 'Glosarium',
                'group' => 'referensi',
                'rows' => 4,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function groupLabels(): array
    {
        return [
            'materi_tujuan' => 'I. Materi & Tujuan Pembelajaran',
            'latihan_evaluasi' => 'II. Latihan & Evaluasi Mandiri',
            'referensi' => 'III. Referensi',
        ];
    }
}
