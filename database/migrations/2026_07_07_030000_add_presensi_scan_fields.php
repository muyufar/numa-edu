<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['siswas', 'gurus', 'pegawais'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('presensi_kode', 32)->nullable()->unique()->after('id');
                $table->json('face_descriptor')->nullable()->after('presensi_kode');
            });
        }

        foreach (['presensi_siswas', 'presensi_gurus', 'presensi_pegawais'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('metode', 16)->default('manual')->after('status');
                $table->time('jam_masuk')->nullable()->after('metode');
            });
        }

        $this->backfillPresensiKode('siswas', 'SIS');
        $this->backfillPresensiKode('gurus', 'GRU');
        $this->backfillPresensiKode('pegawais', 'PEG');
    }

    public function down(): void
    {
        foreach (['siswas', 'gurus', 'pegawais'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropColumn(['presensi_kode', 'face_descriptor']);
            });
        }

        foreach (['presensi_siswas', 'presensi_gurus', 'presensi_pegawais'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropColumn(['metode', 'jam_masuk']);
            });
        }
    }

    private function backfillPresensiKode(string $table, string $prefix): void
    {
        DB::table($table)
            ->where(function ($q): void {
                $q->whereNull('presensi_kode')->orWhere('presensi_kode', '');
            })
            ->orderBy('id')
            ->pluck('id')
            ->each(function (int $id) use ($table, $prefix): void {
                do {
                    $kode = 'NUMA-'.$prefix.'-'.strtoupper(Str::random(12));
                } while (DB::table($table)->where('presensi_kode', $kode)->exists());

                DB::table($table)->where('id', $id)->update(['presensi_kode' => $kode]);
            });
    }
};
