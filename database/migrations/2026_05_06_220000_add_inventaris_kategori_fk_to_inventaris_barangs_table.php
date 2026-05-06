<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris_barangs', function (Blueprint $table) {
            $table
                ->foreign('inventaris_kategori_id')
                ->references('id')
                ->on('inventaris_kategoris')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventaris_barangs', function (Blueprint $table) {
            $table->dropForeign(['inventaris_kategori_id']);
        });
    }
};

