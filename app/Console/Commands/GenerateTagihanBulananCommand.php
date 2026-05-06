<?php

namespace App\Console\Commands;

use App\Support\TagihanGenerator;
use Illuminate\Console\Command;

class GenerateTagihanBulananCommand extends Command
{
    protected $signature = 'keuangan:generate-bulanan
                            {periode? : Format YYYY-MM (default: bulan ini)}
                            {--sekolah_id= : Sekolah ID (default: tenancy.default_sekolah_id)}
                            {--kelas_id= : Batasi hanya 1 kelas}
                            {--dry-run : Hitung saja (belum diimplement)}';

    protected $description = 'Generate tagihan bulanan (SPP, dll) dari Master Kewajiban tipe bulanan.';

    public function handle(): int
    {
        $periode = (string) ($this->argument('periode') ?: now()->format('Y-m'));
        $sekolahId = (int) ($this->option('sekolah_id') ?: config('tenancy.default_sekolah_id', 1));
        $kelasId = $this->option('kelas_id') !== null ? (int) $this->option('kelas_id') : null;

        if ($this->option('dry-run')) {
            $this->warn('dry-run belum diimplement. Jalankan tanpa --dry-run.');

            return self::FAILURE;
        }

        $res = TagihanGenerator::generateBulananForSekolah($periode, $sekolahId, $kelasId);

        $this->info("Periode: {$periode}");
        $this->info("Sekolah: {$sekolahId}".($kelasId ? " | Kelas: {$kelasId}" : ''));
        $this->info("Dibuat: {$res['created']} | Dilewati (sudah ada): {$res['skipped']}");

        return self::SUCCESS;
    }
}

