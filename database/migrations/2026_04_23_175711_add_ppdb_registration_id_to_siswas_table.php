<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->foreignId('ppdb_registration_id')
                ->nullable()
                ->after('user_id')
                ->constrained('ppdb_registrations')
                ->nullOnDelete();
            $table->unique('ppdb_registration_id');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ppdb_registration_id');
        });
    }
};
