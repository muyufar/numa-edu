<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->string('judul', 160);
            $table->text('bahan_materi')->nullable();
            $table->text('instruksi')->nullable();
            $table->string('hari', 16)->nullable();
            $table->time('jam')->nullable();
            $table->date('tanggal_batas')->nullable();
            $table->time('jam_batas')->nullable();
            $table->string('semester', 1)->nullable();
            $table->string('tahun_ajaran', 16)->nullable();
            $table->string('tipe', 32)->default('individu');
            $table->unsignedSmallInteger('bobot')->nullable();
            $table->boolean('is_published')->default(true);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['mata_pelajaran_id', 'kelas_id']);
            $table->index(['tanggal_batas', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
