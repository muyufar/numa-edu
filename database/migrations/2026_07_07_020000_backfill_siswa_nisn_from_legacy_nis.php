<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Data lama: kolom `nis` sering diisi NISN sebelum field dipisah.
        DB::table('siswas')
            ->where(function ($q) {
                $q->whereNull('nisn')->orWhere('nisn', '');
            })
            ->whereNotNull('nis')
            ->where('nis', '!=', '')
            ->update(['nisn' => DB::raw('nis')]);
    }

    public function down(): void
    {
        // Tidak dikembalikan — pemisahan NIS/NISN tidak reversible otomatis.
    }
};
