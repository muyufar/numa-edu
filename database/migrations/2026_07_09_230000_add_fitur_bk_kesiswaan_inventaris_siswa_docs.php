<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table): void {
            $table->string('foto_siswa_path')->nullable()->after('nama_wali');
            $table->string('foto_siswa_name')->nullable()->after('foto_siswa_path');
            $table->string('dok_ijazah_path')->nullable()->after('foto_siswa_name');
            $table->string('dok_ijazah_name')->nullable()->after('dok_ijazah_path');
            $table->string('dok_kk_path')->nullable()->after('dok_ijazah_name');
            $table->string('dok_kk_name')->nullable()->after('dok_kk_path');
            $table->string('dok_ktp_ortu_path')->nullable()->after('dok_kk_name');
            $table->string('dok_ktp_ortu_name')->nullable()->after('dok_ktp_ortu_path');
            $table->string('dok_kip_path')->nullable()->after('dok_ktp_ortu_name');
            $table->string('dok_kip_name')->nullable()->after('dok_kip_path');
            $table->string('dok_kia_path')->nullable()->after('dok_kip_name');
            $table->string('dok_kia_name')->nullable()->after('dok_kia_path');
            $table->string('dok_akta_path')->nullable()->after('dok_kia_name');
            $table->string('dok_akta_name')->nullable()->after('dok_akta_path');
            $table->string('dok_piagam_path')->nullable()->after('dok_akta_name');
            $table->string('dok_piagam_name')->nullable()->after('dok_piagam_path');
        });

        Schema::table('inventaris_barangs', function (Blueprint $table): void {
            $table->string('gambar_path')->nullable()->after('catatan');
            $table->string('gambar_name')->nullable()->after('gambar_path');
        });

        Schema::create('bk_jenis_pelanggarans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('kode', 32);
            $table->string('nama', 120);
            $table->unsignedSmallInteger('poin')->default(1);
            $table->string('tingkat', 16)->default('ringan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['sekolah_id', 'kode'], 'bk_jenis_sekolah_kode_uq');
        });

        Schema::create('bk_sanksis', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nama', 120);
            $table->string('tingkat', 16);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('pelanggarans', function (Blueprint $table): void {
            $table->foreignId('bk_jenis_pelanggaran_id')->nullable()->after('jenis')->constrained('bk_jenis_pelanggarans')->nullOnDelete();
            $table->foreignId('bk_sanksi_id')->nullable()->after('bk_jenis_pelanggaran_id')->constrained('bk_sanksis')->nullOnDelete();
            $table->unsignedSmallInteger('poin')->nullable()->after('bk_sanksi_id');
            $table->string('tingkat', 16)->nullable()->after('poin');
        });

        Schema::create('reward_siswas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('kategori', 24);
            $table->string('judul', 160);
            $table->unsignedSmallInteger('poin')->default(0);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('lomba_ajangs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nama', 160);
            $table->string('tingkat', 64)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('lokasi', 160)->nullable();
            $table->string('penyelenggara', 160)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('lomba_ajang_pesertas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lomba_ajang_id')->constrained('lomba_ajangs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('juara', 64)->nullable();
            $table->string('prestasi', 160)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['lomba_ajang_id', 'siswa_id'], 'lomba_peserta_uq');
        });

        Schema::create('ekstrakurikulers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nama', 160);
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->string('hari', 32)->nullable();
            $table->string('jam', 32)->nullable();
            $table->string('lokasi', 120)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ekstrakurikuler_anggotas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikulers')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('peran', 64)->nullable();
            $table->date('bergabung_tanggal')->nullable();
            $table->timestamps();
            $table->unique(['ekstrakurikuler_id', 'siswa_id'], 'ekskul_anggota_uq');
        });

        Schema::create('ekstrakurikuler_kegiatans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikulers')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('judul', 160);
            $table->text('laporan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('kokurikuler_kegiatans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('judul', 160);
            $table->string('tempat', 160)->nullable();
            $table->date('tanggal');
            $table->text('laporan')->nullable();
            $table->string('lkpd_path')->nullable();
            $table->string('lkpd_name')->nullable();
            $table->string('status', 16)->default('draft');
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('kokurikuler_anggotas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kokurikuler_kegiatan_id')->constrained('kokurikuler_kegiatans')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['kokurikuler_kegiatan_id', 'siswa_id'], 'koku_anggota_uq');
        });

        Schema::create('bk_pemanggilans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('target', 16);
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->date('tanggal_jadwal');
            $table->time('waktu')->nullable();
            $table->string('tempat', 160)->nullable();
            $table->text('alasan');
            $table->string('status', 24)->default('terjadwal');
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bk_home_visits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('foto_path')->nullable();
            $table->string('foto_name')->nullable();
            $table->text('catatan_wawancara')->nullable();
            $table->text('hasil_kunjungan')->nullable();
            $table->text('solusi')->nullable();
            $table->timestamp('dilaporkan_kepsek_at')->nullable();
            $table->string('status', 16)->default('draft');
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bk_home_visits');
        Schema::dropIfExists('bk_pemanggilans');
        Schema::dropIfExists('kokurikuler_anggotas');
        Schema::dropIfExists('kokurikuler_kegiatans');
        Schema::dropIfExists('ekstrakurikuler_kegiatans');
        Schema::dropIfExists('ekstrakurikuler_anggotas');
        Schema::dropIfExists('ekstrakurikulers');
        Schema::dropIfExists('lomba_ajang_pesertas');
        Schema::dropIfExists('lomba_ajangs');
        Schema::dropIfExists('reward_siswas');

        Schema::table('pelanggarans', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('bk_jenis_pelanggaran_id');
            $table->dropConstrainedForeignId('bk_sanksi_id');
            $table->dropColumn(['poin', 'tingkat']);
        });

        Schema::dropIfExists('bk_sanksis');
        Schema::dropIfExists('bk_jenis_pelanggarans');

        Schema::table('inventaris_barangs', function (Blueprint $table): void {
            $table->dropColumn(['gambar_path', 'gambar_name']);
        });

        Schema::table('siswas', function (Blueprint $table): void {
            $table->dropColumn([
                'foto_siswa_path', 'foto_siswa_name',
                'dok_ijazah_path', 'dok_ijazah_name',
                'dok_kk_path', 'dok_kk_name',
                'dok_ktp_ortu_path', 'dok_ktp_ortu_name',
                'dok_kip_path', 'dok_kip_name',
                'dok_kia_path', 'dok_kia_name',
                'dok_akta_path', 'dok_akta_name',
                'dok_piagam_path', 'dok_piagam_name',
            ]);
        });
    }
};
