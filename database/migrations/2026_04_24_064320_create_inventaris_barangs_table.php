<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventaris_barangs', function (Blueprint $table) {
            $table->id();
            // FK added in a follow-up migration to avoid same-timestamp ordering issues.
            $table->foreignId('inventaris_kategori_id')->nullable();
            $table->string('nama', 160);
            $table->string('kode', 64)->nullable();
            $table->string('satuan', 32)->default('unit');
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['inventaris_kategori_id', 'is_active']);
            $table->unique('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_barangs');
    }
};
