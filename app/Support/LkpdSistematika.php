<?php

namespace App\Support;

final class LkpdSistematika
{
    public const META_SISTEMATIKA = '_sistematika';

    /** @var array<string, string> */
    public const ALTERNATIF_OPTIONS = [
        'alternatif_1' => 'Alternatif 1',
        'alternatif_2' => 'Alternatif 2',
    ];

    public static function defaultAlternatif(): string
    {
        return 'alternatif_1';
    }

    public static function resolveAlternatif(?array $konten): string
    {
        $alt = (string) ($konten[self::META_SISTEMATIKA] ?? '');

        return array_key_exists($alt, self::ALTERNATIF_OPTIONS) ? $alt : self::defaultAlternatif();
    }

    public static function labelAlternatif(?string $alternatif): string
    {
        return self::ALTERNATIF_OPTIONS[$alternatif ?? ''] ?? self::ALTERNATIF_OPTIONS[self::defaultAlternatif()];
    }

    /**
     * @return array<string, array{label: string, group: string, rows: int, alternatif: string, placeholder?: string}>
     */
    public static function kontenFieldDefinitions(): array
    {
        return [
            self::META_SISTEMATIKA => [
                'label' => 'Sistematika LKPD',
                'group' => 'meta',
                'rows' => 1,
                'alternatif' => 'both',
            ],
            'indikator_kompetensi' => [
                'label' => 'Indikator Pencapaian Kompetensi',
                'group' => 'isi_lkpd',
                'rows' => 4,
                'alternatif' => 'both',
                'placeholder' => 'Peserta didik mampu ...',
            ],
            'tujuan_pembelajaran' => [
                'label' => 'Tujuan Pembelajaran',
                'group' => 'isi_lkpd',
                'rows' => 4,
                'alternatif' => 'both',
                'placeholder' => 'Setelah menyelesaikan LKPD ini, peserta didik dapat ...',
            ],
            'alat_bahan' => [
                'label' => 'Alat dan Bahan',
                'group' => 'isi_lkpd',
                'rows' => 4,
                'alternatif' => 'alternatif_2',
                'placeholder' => 'Pensil, kertas, LKPD, ...',
            ],
            'petunjuk_belajar' => [
                'label' => 'Petunjuk Belajar',
                'group' => 'isi_lkpd',
                'rows' => 5,
                'alternatif' => 'both',
                'placeholder' => '1. Bacalah ...\n2. Diskusikan dengan kelompok ...',
            ],
            'informasi_pendukung' => [
                'label' => 'Informasi Pendukung',
                'group' => 'isi_lkpd',
                'rows' => 5,
                'alternatif' => 'both',
                'placeholder' => 'Materi singkat, gambar, data, atau penjelasan yang membantu siswa.',
            ],
            'langkah_kerja' => [
                'label' => 'Langkah-langkah Kerja',
                'group' => 'isi_lkpd',
                'rows' => 8,
                'alternatif' => 'both',
                'placeholder' => '1. Amati ...\n2. Catat ...\n3. Analisis ...',
            ],
            'soal_soal' => [
                'label' => 'Soal-soal',
                'group' => 'isi_lkpd',
                'rows' => 10,
                'alternatif' => 'alternatif_1',
                'placeholder' => "1. ...\n2. ...\n3. ...",
            ],
            'tugas_dilakukan' => [
                'label' => 'Tugas yang Harus Dilakukan',
                'group' => 'isi_lkpd',
                'rows' => 8,
                'alternatif' => 'alternatif_2',
                'placeholder' => 'Kerjakan tugas berikut secara berkelompok/individu ...',
            ],
            'hasil_penyelesaian' => [
                'label' => 'Hasil Penyelesaian Tugas',
                'group' => 'isi_lkpd',
                'rows' => 6,
                'alternatif' => 'alternatif_2',
                'placeholder' => 'Format laporan/hasil yang diharapkan dari siswa.',
            ],
        ];
    }

    /**
     * @return array<string, array{label: string, group: string, rows: int, alternatif: string, placeholder?: string}>
     */
    public static function kontenFields(?string $alternatif = null): array
    {
        $definitions = self::kontenFieldDefinitions();

        if ($alternatif === null) {
            return collect($definitions)
                ->except(self::META_SISTEMATIKA)
                ->all();
        }

        return collect($definitions)
            ->except(self::META_SISTEMATIKA)
            ->filter(fn (array $meta) => $meta['alternatif'] === 'both' || $meta['alternatif'] === $alternatif)
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function groupLabels(): array
    {
        return [
            'isi_lkpd' => 'Isi Lembar Kerja Peserta Didik',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $konten
     * @return array<string, string>
     */
    public static function normalizeKonten(?array $konten): array
    {
        $alternatif = self::resolveAlternatif($konten);
        $normalized = [
            self::META_SISTEMATIKA => $alternatif,
        ];

        foreach (array_keys(self::kontenFields()) as $key) {
            $normalized[$key] = trim((string) ($konten[$key] ?? ''));
        }

        return $normalized;
    }

    public static function hasIsi(array $konten): bool
    {
        foreach ($konten as $key => $value) {
            if (str_starts_with((string) $key, '_')) {
                continue;
            }

            if (trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }
}
