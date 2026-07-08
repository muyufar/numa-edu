<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris_barangs', function (Blueprint $table) {
            $table->string('kondisi', 32)->default('normal')->after('is_active');
            $table->index('kondisi');
        });

        Schema::table('inventaris_mutasis', function (Blueprint $table) {
            $table->string('sumber_pengadaan', 32)->nullable()->after('tipe');
        });
    }

    public function down(): void
    {
        Schema::table('inventaris_mutasis', function (Blueprint $table) {
            $table->dropColumn('sumber_pengadaan');
        });

        Schema::table('inventaris_barangs', function (Blueprint $table) {
            $table->dropIndex(['kondisi']);
            $table->dropColumn('kondisi');
        });
    }
};
