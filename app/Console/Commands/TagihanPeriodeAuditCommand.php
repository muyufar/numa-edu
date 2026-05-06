<?php

namespace App\Console\Commands;

use App\Models\Tagihan;
use App\Support\PeriodeBulan;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class TagihanPeriodeAuditCommand extends Command
{
    protected $signature = 'tagihan:periode-audit {--fix : Coba perbaiki format yang bisa dinormalisasi}';

    protected $description = 'Audit (dan opsional perbaiki) format periode tagihan menjadi YYYY-MM.';

    public function handle(): int
    {
        $fix = (bool) $this->option('fix');

        $q = Tagihan::query()->withoutGlobalScopes();
        $total = (int) $q->count();

        $invalid = 0;
        $fixed = 0;

        $q->orderBy('id')->chunkById(200, function ($rows) use ($fix, &$invalid, &$fixed): void {
            foreach ($rows as $t) {
                $p = (string) $t->periode;
                if (preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $p) === 1) {
                    continue;
                }

                $invalid++;

                $norm = PeriodeBulan::normalize($p);
                if (! $norm && preg_match('/^\d{4}\s*\/\s*\d{4}$/', trim($p)) === 1) {
                    $basis = $t->jatuh_tempo ?: $t->created_at;
                    if ($basis) {
                        $norm = Carbon::parse($basis)->format('Y-m');
                    }
                }

                if ($fix && $norm) {
                    $t->forceFill(['periode' => $norm])->save();
                    $fixed++;
                    $this->line("FIX  #{$t->id}  {$p}  ->  {$norm}");
                } else {
                    $this->line("BAD  #{$t->id}  {$p}");
                }
            }
        });

        $this->newLine();
        $this->info("Total tagihan: {$total}");
        $this->warn("Periode tidak valid: {$invalid}");
        if ($fix) {
            $this->info("Berhasil diperbaiki: {$fixed}");
        }

        return self::SUCCESS;
    }
}

