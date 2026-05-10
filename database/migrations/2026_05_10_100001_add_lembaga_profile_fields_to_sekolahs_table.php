<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('npwp', 32)->nullable()->after('website');
            $table->string('medsos', 512)->nullable()->after('npwp');
            $table->unsignedSmallInteger('tahun_berdiri')->nullable()->after('medsos');
            $table->string('waktu_belajar', 32)->nullable()->after('tahun_berdiri');
            $table->string('status_kkm', 32)->nullable()->after('waktu_belajar');
            $table->string('komite', 16)->nullable()->after('status_kkm');
            $table->string('rt', 8)->nullable()->after('komite');
            $table->string('rw', 8)->nullable()->after('rt');
            $table->string('kodepos', 16)->nullable()->after('rw');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn([
                'npwp',
                'medsos',
                'tahun_berdiri',
                'waktu_belajar',
                'status_kkm',
                'komite',
                'rt',
                'rw',
                'kodepos',
            ]);
        });
    }
};
