<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->string('jenis_soal', 32)->default('esai')->after('judul');
        });

        Schema::create('tugas_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->text('pertanyaan');
            $table->timestamps();

            $table->index(['tugas_id', 'urutan']);
        });

        Schema::create('tugas_pilihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_soal_id')->constrained('tugas_soals')->cascadeOnDelete();
            $table->string('label', 2);
            $table->string('teks', 500);
            $table->boolean('is_benar')->default(false);
            $table->timestamps();

            $table->index(['tugas_soal_id', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_pilihans');
        Schema::dropIfExists('tugas_soals');

        Schema::table('tugas', function (Blueprint $table) {
            $table->dropColumn('jenis_soal');
        });
    }
};
