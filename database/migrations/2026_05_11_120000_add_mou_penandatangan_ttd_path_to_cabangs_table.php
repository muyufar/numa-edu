<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            if (! Schema::hasColumn('cabangs', 'mou_penandatangan_ttd_path')) {
                $table->string('mou_penandatangan_ttd_path', 2048)->nullable()->after('mou_stempel_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            if (Schema::hasColumn('cabangs', 'mou_penandatangan_ttd_path')) {
                $table->dropColumn('mou_penandatangan_ttd_path');
            }
        });
    }
};
