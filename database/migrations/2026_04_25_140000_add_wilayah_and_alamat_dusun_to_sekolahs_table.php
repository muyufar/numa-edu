<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('kode_provinsi', 16)->nullable()->after('alamat');
            $table->string('nama_provinsi', 191)->nullable()->after('kode_provinsi');
            $table->string('kode_kabupaten', 24)->nullable()->after('nama_provinsi');
            $table->string('nama_kabupaten', 191)->nullable()->after('kode_kabupaten');
            $table->string('kode_kecamatan', 24)->nullable()->after('nama_kabupaten');
            $table->string('nama_kecamatan', 191)->nullable()->after('kode_kecamatan');
            $table->string('kode_kelurahan', 24)->nullable()->after('nama_kecamatan');
            $table->string('nama_kelurahan', 191)->nullable()->after('kode_kelurahan');
            $table->text('alamat_dusun')->nullable()->after('nama_kelurahan');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn([
                'kode_provinsi',
                'nama_provinsi',
                'kode_kabupaten',
                'nama_kabupaten',
                'kode_kecamatan',
                'nama_kecamatan',
                'kode_kelurahan',
                'nama_kelurahan',
                'alamat_dusun',
            ]);
        });
    }
};
