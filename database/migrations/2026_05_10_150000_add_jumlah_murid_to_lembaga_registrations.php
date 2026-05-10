<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lembaga_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('lembaga_registrations', 'jumlah_murid')) {
                $table->unsignedInteger('jumlah_murid')->nullable()->after('komite');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lembaga_registrations', function (Blueprint $table) {
            $table->dropColumn('jumlah_murid');
        });
    }
};
