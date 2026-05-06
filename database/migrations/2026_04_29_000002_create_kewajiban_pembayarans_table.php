<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kewajiban_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nama', 64);
            $table->string('tipe', 16); // bulanan|insidental
            $table->decimal('nominal_default', 12, 2)->default(0);
            $table->string('berlaku_mulai', 16)->nullable(); // contoh: 2025-07
            $table->unsignedTinyInteger('batas_hari_bayar')->nullable(); // contoh: 15 (tgl 15 tiap bulan)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['sekolah_id', 'tipe', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kewajiban_pembayarans');
    }
};

