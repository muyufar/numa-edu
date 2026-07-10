<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['gurus', 'pegawais'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('foto_path')->nullable();
                $table->string('foto_name')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['gurus', 'pegawais'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn(['foto_path', 'foto_name']);
            });
        }
    }
};
