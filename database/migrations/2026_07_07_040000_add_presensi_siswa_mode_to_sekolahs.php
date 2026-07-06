<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sekolahs', 'presensi_siswa_mode')) {
            Schema::table('sekolahs', function (Blueprint $table): void {
                $table->string('presensi_siswa_mode', 16)->default('harian')->after('is_active');
            });
        }

        if (! Schema::hasColumn('presensi_siswas', 'jadwal_id')) {
            Schema::table('presensi_siswas', function (Blueprint $table): void {
                $table->foreignId('jadwal_id')->nullable()->after('siswa_id')->constrained('jadwals')->nullOnDelete();
                $table->string('presensi_slot', 32)->default('harian')->after('jadwal_id');
            });
        }

        $hasNewUnique = collect(Schema::getIndexes('presensi_siswas'))
            ->contains(fn (array $index) => ($index['name'] ?? '') === 'presensi_siswas_siswa_id_tanggal_presensi_slot_unique');

        if (! $hasNewUnique) {
            Schema::table('presensi_siswas', function (Blueprint $table): void {
                $table->dropForeign(['siswa_id']);
            });

            Schema::table('presensi_siswas', function (Blueprint $table): void {
                $table->dropUnique(['siswa_id', 'tanggal']);
                $table->unique(['siswa_id', 'tanggal', 'presensi_slot']);
                $table->foreign('siswa_id')->references('id')->on('siswas')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('presensi_siswas', 'jadwal_id')) {
            Schema::table('presensi_siswas', function (Blueprint $table): void {
                $table->dropForeign(['siswa_id']);
            });

            Schema::table('presensi_siswas', function (Blueprint $table): void {
                $table->dropUnique(['siswa_id', 'tanggal', 'presensi_slot']);
                $table->unique(['siswa_id', 'tanggal']);
                $table->foreign('siswa_id')->references('id')->on('siswas')->cascadeOnDelete();
            });

            Schema::table('presensi_siswas', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('jadwal_id');
                $table->dropColumn('presensi_slot');
            });
        }

        if (Schema::hasColumn('sekolahs', 'presensi_siswa_mode')) {
            Schema::table('sekolahs', function (Blueprint $table): void {
                $table->dropColumn('presensi_siswa_mode');
            });
        }
    }
};
