<?php

namespace App\Support;

final class PerangkatAjarJenis
{
    /** @var list<string> */
    public const STRUKTUR_DIGITAL = ['modul', 'rpp', 'modul_pembelajaran', 'lkpd'];

    public static function supportsKontenDigital(?string $jenis): bool
    {
        return in_array($jenis, self::STRUKTUR_DIGITAL, true);
    }

    /**
     * @return array<string, array{label: string, group: string, rows: int, placeholder?: string}>
     */
    public static function kontenFields(?string $jenis): array
    {
        return match ($jenis) {
            'modul' => ModulAjarMerdeka::kontenFields(),
            'rpp' => RppMerdeka::kontenFields(),
            'modul_pembelajaran' => ModulPembelajaran::kontenFields(),
            'lkpd' => LkpdSistematika::kontenFields(),
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    public static function groupLabels(?string $jenis): array
    {
        return match ($jenis) {
            'modul' => ModulAjarMerdeka::groupLabels(),
            'rpp' => RppMerdeka::groupLabels(),
            'modul_pembelajaran' => ModulPembelajaran::groupLabels(),
            'lkpd' => LkpdSistematika::groupLabels(),
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>|null  $konten
     * @return array<string, string>
     */
    public static function normalizeKonten(?string $jenis, ?array $konten): array
    {
        if ($jenis === 'lkpd') {
            return LkpdSistematika::normalizeKonten($konten);
        }

        $normalized = [];
        foreach (array_keys(self::kontenFields($jenis)) as $key) {
            $normalized[$key] = trim((string) ($konten[$key] ?? ''));
        }

        return $normalized;
    }

    public static function hasIsi(?string $jenis, array $konten): bool
    {
        if ($jenis === 'lkpd') {
            return LkpdSistematika::hasIsi($konten);
        }

        foreach ($konten as $value) {
            if (trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }

    public static function deskripsiJenis(?string $jenis): string
    {
        return match ($jenis) {
            'modul' => __('Panduan belajar berpusat pada siswa — mendukung pembelajaran mandiri atau tatap muka dengan asesmen formatif & autentik.'),
            'rpp' => __('Rencana pelaksanaan pembelajaran berpusat pada guru — berisi langkah-langkah mengelola pembelajaran di kelas.'),
            'modul_pembelajaran' => __('Bahan ajar terstruktur untuk belajar mandiri — memuat materi lengkap, latihan soal, dan evaluasi mandiri.'),
            'lkpd' => __('Lembar kerja peserta didik — panduan kerja siswa berisi petunjuk, langkah kerja, dan soal/tugas sesuai sistematika LKPD.'),
            default => '',
        };
    }

    public static function fokusJenis(?string $jenis): string
    {
        return match ($jenis) {
            'modul' => __('Siswa'),
            'rpp' => __('Guru'),
            'modul_pembelajaran' => __('Siswa (mandiri)'),
            'lkpd' => __('Siswa'),
            default => '—',
        };
    }

    /**
     * Ringkasan perbandingan tiga jenis utama (sesuai dokumen).
     *
     * @return list<array{jenis: string, label: string, fokus: string, tujuan: string, isi: string, evaluasi: string}>
     */
    public static function perbandingan(): array
    {
        return [
            [
                'jenis' => 'modul',
                'label' => __('Modul Ajar'),
                'fokus' => __('Siswa'),
                'tujuan' => __('Mendukung pembelajaran aktif atau mandiri'),
                'isi' => __('Tujuan, materi, aktivitas, evaluasi'),
                'evaluasi' => __('Asesmen formatif & autentik (proses)'),
            ],
            [
                'jenis' => 'rpp',
                'label' => __('RPP'),
                'fokus' => __('Guru'),
                'tujuan' => __('Menjalankan pembelajaran sesuai rencana di kelas'),
                'isi' => __('Langkah-langkah pembelajaran & penilaian'),
                'evaluasi' => __('Penilaian hasil belajar sesuai rencana'),
            ],
            [
                'jenis' => 'modul_pembelajaran',
                'label' => __('Modul Pembelajaran'),
                'fokus' => __('Siswa'),
                'tujuan' => __('Belajar mandiri kapan saja'),
                'isi' => __('Materi ajar, latihan soal, evaluasi mandiri'),
                'evaluasi' => __('Evaluasi mandiri (latihan/tes)'),
            ],
        ];
    }
}
