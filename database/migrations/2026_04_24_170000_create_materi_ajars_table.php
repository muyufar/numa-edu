<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi_ajars', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('semester', 2)->nullable();
            $table->string('tahun_ajaran', 16)->nullable();
            $table->date('tanggal')->nullable();

            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['mata_pelajaran_id', 'kelas_id']);
            $table->index(['tahun_ajaran', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_ajars');
    }
};

