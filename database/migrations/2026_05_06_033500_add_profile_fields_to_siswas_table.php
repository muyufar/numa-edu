<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nisn', 32)->nullable()->after('nis');
            $table->string('nik', 32)->nullable()->after('nisn');
            $table->string('tempat_lahir')->nullable()->after('nik');
            $table->string('tingkat_rombel')->nullable()->after('tempat_lahir');
            $table->string('umur')->nullable()->after('tingkat_rombel');
            $table->string('status')->nullable()->after('umur');
            $table->string('no_telepon', 32)->nullable()->after('alamat');
            $table->string('kebutuhan_khusus')->nullable()->after('no_telepon');
            $table->string('disabilitas')->nullable()->after('kebutuhan_khusus');
            $table->string('nomor_kip_pip', 64)->nullable()->after('disabilitas');
            $table->string('nama_ayah_kandung')->nullable()->after('nomor_kip_pip');
            $table->string('nama_ibu_kandung')->nullable()->after('nama_ayah_kandung');
            $table->string('nama_wali')->nullable()->after('nama_ibu_kandung');

            $table->unique('nisn');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropUnique(['nisn']);
            $table->dropColumn([
                'nisn',
                'nik',
                'tempat_lahir',
                'tingkat_rombel',
                'umur',
                'status',
                'no_telepon',
                'kebutuhan_khusus',
                'disabilitas',
                'nomor_kip_pip',
                'nama_ayah_kandung',
                'nama_ibu_kandung',
                'nama_wali',
            ]);
        });
    }
};

