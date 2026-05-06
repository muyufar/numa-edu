<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('tagihans')) {
            return;
        }

        DB::table('tagihans')
            ->select(['id', 'periode', 'jatuh_tempo', 'created_at'])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $r) {
                    $periode = is_string($r->periode) ? trim($r->periode) : '';
                    if ($periode === '') {
                        continue;
                    }

                    // Sudah valid YYYY-MM
                    if (preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $periode) === 1) {
                        continue;
                    }

                    // Kasus umum lama: "2025/2026" (tahun ajaran)
                    if (preg_match('/^\d{4}\s*\/\s*\d{4}$/', $periode) !== 1) {
                        continue;
                    }

                    $basis = $r->jatuh_tempo ?: $r->created_at;
                    if (! $basis) {
                        continue;
                    }

                    try {
                        $dt = Carbon::parse($basis);
                    } catch (Throwable) {
                        continue;
                    }

                    $normalized = $dt->format('Y-m');

                    DB::table('tagihans')
                        ->where('id', $r->id)
                        ->update(['periode' => $normalized]);
                }
            });
    }

    public function down(): void
    {
        // Tidak aman mengembalikan nilai periode tahun ajaran.
    }
};

