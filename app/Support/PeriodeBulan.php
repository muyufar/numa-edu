<?php

namespace App\Support;

class PeriodeBulan
{
    /**
     * Normalisasi input periode menjadi format YYYY-MM.
     * Menerima contoh: 2026-04, 2026/04, 04/2026, Apr 2026, April 2026, apr-2026.
     */
    public static function normalize(string $input): ?string
    {
        $raw = trim($input);
        if ($raw === '') {
            return null;
        }

        // Sudah benar
        if (preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $raw) === 1) {
            return $raw;
        }

        // YYYY/MM
        if (preg_match('/^(\d{4})\/(0?[1-9]|1[0-2])$/', $raw, $m) === 1) {
            return sprintf('%04d-%02d', (int) $m[1], (int) $m[2]);
        }

        // MM/YYYY atau M/YYYY
        if (preg_match('/^(0?[1-9]|1[0-2])\/(\d{4})$/', $raw, $m) === 1) {
            return sprintf('%04d-%02d', (int) $m[2], (int) $m[1]);
        }

        // MM-YYYY atau MM YYYY
        if (preg_match('/^(0?[1-9]|1[0-2])[\-\s](\d{4})$/', $raw, $m) === 1) {
            return sprintf('%04d-%02d', (int) $m[2], (int) $m[1]);
        }

        // YYYY MM
        if (preg_match('/^(\d{4})[\-\s](0?[1-9]|1[0-2])$/', $raw, $m) === 1) {
            return sprintf('%04d-%02d', (int) $m[1], (int) $m[2]);
        }

        // Month name + year
        $lower = mb_strtolower($raw);
        $lower = preg_replace('/[.,]/', ' ', $lower);
        $lower = preg_replace('/\s+/', ' ', $lower);
        $lower = trim((string) $lower);

        $bulanMap = [
            'jan' => 1, 'januari' => 1, 'january' => 1,
            'feb' => 2, 'februari' => 2, 'february' => 2,
            'mar' => 3, 'maret' => 3, 'march' => 3,
            'apr' => 4, 'april' => 4,
            'mei' => 5, 'may' => 5,
            'jun' => 6, 'juni' => 6, 'june' => 6,
            'jul' => 7, 'juli' => 7, 'july' => 7,
            'agu' => 8, 'agustus' => 8, 'aug' => 8, 'august' => 8,
            'sep' => 9, 'september' => 9,
            'okt' => 10, 'oct' => 10, 'oktober' => 10, 'october' => 10,
            'nov' => 11, 'november' => 11,
            'des' => 12, 'dec' => 12, 'desember' => 12, 'december' => 12,
        ];

        if (preg_match('/^([a-z]+)[\-\s](\d{4})$/', $lower, $m) === 1) {
            $mon = $bulanMap[$m[1]] ?? null;
            if ($mon) {
                return sprintf('%04d-%02d', (int) $m[2], (int) $mon);
            }
        }

        if (preg_match('/^(\d{4})[\-\s]([a-z]+)$/', $lower, $m) === 1) {
            $mon = $bulanMap[$m[2]] ?? null;
            if ($mon) {
                return sprintf('%04d-%02d', (int) $m[1], (int) $mon);
            }
        }

        // Year range (mis. 2025/2026) tidak bisa disetarakan ke bulan
        if (preg_match('/^\d{4}\s*\/\s*\d{4}$/', $raw) === 1) {
            return null;
        }

        return null;
    }

    /**
     * Urutkan dua periode valid YYYY-MM agar dari ≤ sampai (string comparison aman untuk format ini).
     *
     * @return array{0: string, 1: string}
     */
    public static function orderMonths(string $from, string $to): array
    {
        if (strcmp($from, $to) > 0) {
            return [$to, $from];
        }

        return [$from, $to];
    }
}

