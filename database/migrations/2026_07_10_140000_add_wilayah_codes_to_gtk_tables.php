<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['gurus', 'pegawais'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('kode_provinsi', 16)->nullable();
                $table->string('nama_provinsi', 191)->nullable();
                $table->string('kode_kabupaten', 24)->nullable();
                $table->string('nama_kabupaten', 191)->nullable();
                $table->string('kode_kecamatan', 24)->nullable();
                $table->string('nama_kecamatan', 191)->nullable();
                $table->string('kode_kelurahan', 24)->nullable();
                $table->string('nama_kelurahan', 191)->nullable();
                $table->text('alamat_dusun')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['gurus', 'pegawais'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn([
                    'kode_provinsi', 'nama_provinsi', 'kode_kabupaten', 'nama_kabupaten',
                    'kode_kecamatan', 'nama_kecamatan', 'kode_kelurahan', 'nama_kelurahan',
                    'alamat_dusun',
                ]);
            });
        }
    }
};
