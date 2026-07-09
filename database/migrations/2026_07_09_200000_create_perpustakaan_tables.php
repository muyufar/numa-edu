<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perpustakaan_kategoris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nama', 120);
            $table->string('kode', 32)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->index(['sekolah_id', 'nama']);
        });

        Schema::create('perpustakaan_bukus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('perpustakaan_kategori_id')->nullable()->constrained('perpustakaan_kategoris')->nullOnDelete();
            $table->string('judul', 200);
            $table->string('pengarang', 160)->nullable();
            $table->string('penerbit', 120)->nullable();
            $table->unsignedSmallInteger('tahun_terbit')->nullable();
            $table->string('isbn', 32)->nullable();
            $table->string('tipe', 16)->default('fisik'); // fisik, digital, fisik_digital
            $table->unsignedInteger('jumlah_eksemplar')->default(1);
            $table->unsignedInteger('eksemplar_tersedia')->default(1);
            $table->string('rak_lokasi', 64)->nullable();
            $table->string('bahasa', 16)->default('id');
            $table->text('sinopsis')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('cover_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['sekolah_id', 'tipe', 'is_active']);
            $table->index(['sekolah_id', 'judul']);
        });

        Schema::create('perpustakaan_pengaturans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->unique()->constrained('sekolahs')->cascadeOnDelete();
            $table->unsignedTinyInteger('max_peminjaman_aktif')->default(3);
            $table->unsignedSmallInteger('masa_pinjam_fisik_hari')->default(7);
            $table->unsignedSmallInteger('masa_pinjam_digital_hari')->default(14);
            $table->unsignedInteger('denda_per_hari')->default(1000);
            $table->unsignedTinyInteger('max_perpanjangan')->default(1);
            $table->timestamps();
        });

        Schema::create('perpustakaan_peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('perpustakaan_buku_id')->constrained('perpustakaan_bukus')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('siswa_id')->nullable()->constrained('siswas')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->string('tipe_peminjaman', 16); // fisik, digital
            $table->string('status', 16)->default('dipinjam'); // dipinjam, dikembalikan, terlambat, hilang
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_kembali')->nullable();
            $table->unsignedTinyInteger('jumlah_perpanjangan')->default(0);
            $table->unsignedInteger('denda')->default(0);
            $table->text('catatan')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sekolah_id', 'status', 'tanggal_jatuh_tempo'], 'perpus_pinjam_stat_jt_idx');
            $table->index(['user_id', 'status'], 'perpus_pinjam_user_stat_idx');
            $table->index(['perpustakaan_buku_id', 'status'], 'perpus_pinjam_buku_stat_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perpustakaan_peminjamans');
        Schema::dropIfExists('perpustakaan_pengaturans');
        Schema::dropIfExists('perpustakaan_bukus');
        Schema::dropIfExists('perpustakaan_kategoris');
    }
};
