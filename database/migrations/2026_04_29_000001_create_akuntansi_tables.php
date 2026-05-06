<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akuntansi_akuns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('kode', 32);
            $table->string('nama');
            $table->string('tipe', 16); // aset|kewajiban|ekuitas|pendapatan|beban
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['sekolah_id', 'kode']);
            $table->index(['sekolah_id', 'tipe']);
        });

        Schema::create('akuntansi_jurnals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('no_bukti', 64)->nullable();
            $table->string('keterangan')->nullable();
            $table->nullableMorphs('sumber'); // sumber_type, sumber_id
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sekolah_id', 'tanggal']);
        });

        Schema::create('akuntansi_jurnal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('jurnal_id')->constrained('akuntansi_jurnals')->cascadeOnDelete();
            $table->foreignId('akun_id')->constrained('akuntansi_akuns')->cascadeOnDelete();
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('kredit', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['sekolah_id', 'akun_id']);
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->foreignId('akuntansi_jurnal_id')
                ->nullable()
                ->after('dicatat_oleh')
                ->constrained('akuntansi_jurnals')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('akuntansi_jurnal_id');
        });

        Schema::dropIfExists('akuntansi_jurnal_lines');
        Schema::dropIfExists('akuntansi_jurnals');
        Schema::dropIfExists('akuntansi_akuns');
    }
};

