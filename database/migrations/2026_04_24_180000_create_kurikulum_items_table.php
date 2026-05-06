<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurikulum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->unsignedTinyInteger('tingkat');
            $table->string('semester', 2);
            $table->string('tahun_ajaran', 16);
            $table->unsignedTinyInteger('jam_per_minggu')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['mata_pelajaran_id', 'tingkat', 'semester', 'tahun_ajaran'], 'kurikulum_items_unique_mapel_tingkat_semester_ta');
            $table->index(['tahun_ajaran', 'semester', 'tingkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum_items');
    }
};
