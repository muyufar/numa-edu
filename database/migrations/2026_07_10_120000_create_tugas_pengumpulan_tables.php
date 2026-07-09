<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas_pengumpulans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->text('jawaban_esai')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedSmallInteger('nilai_otomatis')->nullable();
            $table->string('status', 16)->default('submitted');
            $table->timestamp('dikumpulkan_pada')->nullable();
            $table->timestamps();

            $table->unique(['tugas_id', 'siswa_id'], 'tugas_pengumpulan_uq');
        });

        Schema::create('tugas_jawaban_pilihans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tugas_pengumpulan_id')->constrained('tugas_pengumpulans')->cascadeOnDelete();
            $table->foreignId('tugas_soal_id')->constrained('tugas_soals')->cascadeOnDelete();
            $table->foreignId('tugas_pilihan_id')->constrained('tugas_pilihans')->cascadeOnDelete();
            $table->boolean('is_benar')->default(false);
            $table->timestamps();

            $table->unique(['tugas_pengumpulan_id', 'tugas_soal_id'], 'tugas_jawaban_soal_uq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_jawaban_pilihans');
        Schema::dropIfExists('tugas_pengumpulans');
    }
};
