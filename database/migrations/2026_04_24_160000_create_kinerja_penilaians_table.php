<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kinerja_penilaians', function (Blueprint $table) {
            $table->id();

            $table->string('target_type'); // guru|pegawai
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();

            $table->date('tanggal');
            $table->string('periode'); // YYYY-MM

            $table->string('aspek');
            $table->unsignedTinyInteger('skor'); // 0-100
            $table->text('catatan')->nullable();

            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['target_type', 'periode']);
            $table->index(['guru_id', 'periode']);
            $table->index(['pegawai_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinerja_penilaians');
    }
};

