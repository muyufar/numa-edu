<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengeluaran_kass', function (Blueprint $table) {
            $table->string('bukti_nota_path')->nullable()->after('no_bukti');
        });

        Schema::create('pemasukan_kass', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('jumlah', 12, 2);
            $table->string('keterangan');
            $table->string('no_bukti', 64)->nullable();
            $table->string('bukti_nota_path')->nullable();
            $table->foreignId('akun_pendapatan_id')->nullable()->constrained('akuntansi_akuns')->restrictOnDelete();
            $table->unsignedBigInteger('akuntansi_jurnal_id')->nullable()->index();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sekolah_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan_kass');

        Schema::table('pengeluaran_kass', function (Blueprint $table) {
            $table->dropColumn('bukti_nota_path');
        });
    }
};
