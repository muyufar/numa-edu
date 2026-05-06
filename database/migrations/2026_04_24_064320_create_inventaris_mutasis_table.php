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
        Schema::create('inventaris_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventaris_barang_id')->constrained('inventaris_barangs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('tipe', 16); // in|out|adjust
            $table->integer('jumlah'); // >0
            $table->string('referensi', 120)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['inventaris_barang_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_mutasis');
    }
};
