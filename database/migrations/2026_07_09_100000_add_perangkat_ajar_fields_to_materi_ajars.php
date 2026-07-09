<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi_ajars', function (Blueprint $table) {
            $table->string('jenis', 32)->default('lainnya')->after('judul');
            $table->string('status_publikasi', 24)->default('dipublikasi')->after('deskripsi');
            $table->string('status_penggunaan', 24)->default('aktif')->after('status_publikasi');
            $table->unsignedSmallInteger('pertemuan_ke')->nullable()->after('status_penggunaan');
            $table->timestamp('dipublikasi_pada')->nullable()->after('diunggah_oleh');
            $table->timestamp('diarsipkan_pada')->nullable()->after('dipublikasi_pada');

            $table->index(['status_publikasi', 'status_penggunaan']);
            $table->index(['jenis', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::table('materi_ajars', function (Blueprint $table) {
            $table->dropIndex(['status_publikasi', 'status_penggunaan']);
            $table->dropIndex(['jenis', 'tahun_ajaran']);
            $table->dropColumn([
                'jenis',
                'status_publikasi',
                'status_penggunaan',
                'pertemuan_ke',
                'dipublikasi_pada',
                'diarsipkan_pada',
            ]);
        });
    }
};
