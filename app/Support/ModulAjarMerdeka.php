<?php

namespace App\Support;

final class ModulAjarMerdeka
{
    /** @var list<string> */
    public const FASE_OPTIONS = ['A', 'B', 'C', 'D', 'E', 'F'];

    /** @var list<string> */
    public const MODEL_PEMBELAJARAN_OPTIONS = [
        'Discovery Learning',
        'Problem Based Learning',
        'Project Based Learning',
        'Cooperative Learning',
        'Direct Instruction',
        'Inquiry Learning',
        'Blended Learning',
    ];

    /**
     * Struktur kolom mengikuti format Modul Ajar Kurikulum Merdeka.
     *
     * @return array<string, array{label: string, group: string, rows: int, placeholder?: string}>
     */
    public static function kontenFields(): array
    {
        return [
            'capaian_pembelajaran' => [
                'label' => 'Capaian Pembelajaran (CP)',
                'group' => 'informasi_umum',
                'rows' => 4,
                'placeholder' => 'Peserta didik dapat ...',
            ],
            'profil_pelajar_pancasila' => [
                'label' => 'Profil Pelajar Pancasila',
                'group' => 'informasi_umum',
                'rows' => 5,
                'placeholder' => 'Beriman, Bertakwa... Mandiri... Bernalar Kritis...',
            ],
            'sarana_prasarana' => [
                'label' => 'Sarana & Prasarana',
                'group' => 'informasi_umum',
                'rows' => 4,
                'placeholder' => 'Ruang kelas, papan tulis, LKPD, proyektor, ...',
            ],
            'tujuan_pembelajaran' => [
                'label' => 'Tujuan Pembelajaran (TP)',
                'group' => 'komponen_inti',
                'rows' => 4,
                'placeholder' => 'Melalui kegiatan ..., peserta didik dapat ... (ABCD)',
            ],
            'alur_tujuan_pembelajaran' => [
                'label' => 'Posisi dalam Alur Tujuan Pembelajaran (ATP)',
                'group' => 'komponen_inti',
                'rows' => 3,
            ],
            'pemahaman_bermakna' => [
                'label' => 'Pemahaman Bermakna',
                'group' => 'komponen_inti',
                'rows' => 4,
            ],
            'pertanyaan_pemantik' => [
                'label' => 'Pertanyaan Pemantik',
                'group' => 'komponen_inti',
                'rows' => 4,
                'placeholder' => "1. ...\n2. ...\n3. ...",
            ],
            'kegiatan_pembelajaran' => [
                'label' => 'Kegiatan Pembelajaran',
                'group' => 'komponen_inti',
                'rows' => 12,
                'placeholder' => 'Pendahuluan, Kegiatan Inti (Discovery Learning), Penutup...',
            ],
            'asesmen_diagnostik' => [
                'label' => 'Asesmen Diagnostik',
                'group' => 'asesmen',
                'rows' => 5,
                'placeholder' => 'Teknik, Instrumen, Tujuan',
            ],
            'asesmen_formatif' => [
                'label' => 'Asesmen Formatif',
                'group' => 'asesmen',
                'rows' => 6,
                'placeholder' => 'Teknik, Instrumen, Tujuan',
            ],
            'asesmen_sumatif' => [
                'label' => 'Asesmen Sumatif',
                'group' => 'asesmen',
                'rows' => 5,
                'placeholder' => 'Teknik, Instrumen, Tujuan',
            ],
            'lampiran_lkpd' => [
                'label' => 'Lembar Kerja Peserta Didik (LKPD)',
                'group' => 'lampiran',
                'rows' => 6,
            ],
            'bahan_bacaan' => [
                'label' => 'Bahan Bacaan Guru & Peserta Didik',
                'group' => 'lampiran',
                'rows' => 6,
            ],
            'glosarium' => [
                'label' => 'Glosarium',
                'group' => 'lampiran',
                'rows' => 4,
            ],
            'daftar_pustaka' => [
                'label' => 'Daftar Pustaka',
                'group' => 'lampiran',
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
            'informasi_umum' => 'I. Informasi Umum',
            'komponen_inti' => 'II. Komponen Inti (Deep Learning)',
            'asesmen' => 'III. Asesmen',
            'lampiran' => 'IV. Lampiran',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $konten
     * @return array<string, string>
     */
    public static function normalizeKonten(?array $konten): array
    {
        $normalized = [];
        foreach (array_keys(self::kontenFields()) as $key) {
            $normalized[$key] = trim((string) ($konten[$key] ?? ''));
        }

        return $normalized;
    }

    public static function hasIsi(array $konten): bool
    {
        foreach ($konten as $value) {
            if (trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }
}
