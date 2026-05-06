<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        \App\Console\Commands\TagihanPeriodeAuditCommand::class,
        \App\Console\Commands\GenerateTagihanBulananCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Jalankan setiap tanggal 1 jam 00:10 untuk periode bulan berjalan.
        // Catatan: butuh cron "php artisan schedule:run" tiap menit.
        $schedule->command('keuangan:generate-bulanan')->monthlyOn(1, '00:10')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

