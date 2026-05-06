<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function normalize(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $raw = trim($input);
        if ($raw === '') {
            return null;
        }

        if (preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $raw) === 1) {
            return $raw;
        }

        if (preg_match('/^(\d{4})\/(0?[1-9]|1[0-2])$/', $raw, $m) === 1) {
            return sprintf('%04d-%02d', (int) $m[1], (int) $m[2]);
        }

        if (preg_match('/^(0?[1-9]|1[0-2])\/(\d{4})$/', $raw, $m) === 1) {
            return sprintf('%04d-%02d', (int) $m[2], (int) $m[1]);
        }

        if (preg_match('/^(0?[1-9]|1[0-2])[\-\s](\d{4})$/', $raw, $m) === 1) {
            return sprintf('%04d-%02d', (int) $m[2], (int) $m[1]);
        }

        if (preg_match('/^(\d{4})[\-\s](0?[1-9]|1[0-2])$/', $raw, $m) === 1) {
            return sprintf('%04d-%02d', (int) $m[1], (int) $m[2]);
        }

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

        return null;
    }

    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('tagihans')) {
            return;
        }

        DB::table('tagihans')
            ->select(['id', 'periode'])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $r) {
                    $norm = $this->normalize($r->periode);
                    if ($norm && $norm !== $r->periode) {
                        DB::table('tagihans')->where('id', $r->id)->update(['periode' => $norm]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Tidak aman mengembalikan format lama.
    }
};

