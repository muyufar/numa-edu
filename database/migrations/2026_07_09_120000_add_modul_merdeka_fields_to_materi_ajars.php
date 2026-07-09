<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi_ajars', function (Blueprint $table) {
            $table->string('fase', 8)->nullable()->after('jenis');
            $table->string('elemen_topik', 200)->nullable()->after('fase');
            $table->string('alokasi_waktu', 64)->nullable()->after('elemen_topik');
            $table->string('model_pembelajaran', 120)->nullable()->after('alokasi_waktu');
            $table->json('konten_modul')->nullable()->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('materi_ajars', function (Blueprint $table) {
            $table->dropColumn([
                'fase',
                'elemen_topik',
                'alokasi_waktu',
                'model_pembelajaran',
                'konten_modul',
            ]);
        });
    }
};
