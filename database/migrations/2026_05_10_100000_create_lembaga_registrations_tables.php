<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Setengah migrasi sebelumnya bisa meninggalkan `lembaga_registrations` tanpa tabel permits
        // dan tanpa baris di `migrations` — hindari error "table already exists".
        if (! Schema::hasTable('lembaga_registrations')) {
            Schema::create('lembaga_registrations', function (Blueprint $table) {
                $table->id();
                $table->uuid('public_token')->unique();
                $table->string('status', 32)->default('awaiting_mou')->index();

                $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->nullOnDelete();
                $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->nullOnDelete();

                $table->string('npsn', 16);
                $table->string('nama_lembaga');
                $table->string('nama_kepala')->nullable();
                $table->string('jenjang', 16);
                $table->string('npwp', 32)->nullable();
                $table->string('telepon', 64)->nullable();
                $table->string('website', 512)->nullable();
                $table->string('email')->nullable();
                $table->string('medsos', 512)->nullable();
                $table->unsignedSmallInteger('tahun_berdiri')->nullable();
                $table->string('waktu_belajar', 32);
                $table->string('status_kkm', 32);
                $table->string('komite', 16);

                $table->text('alamat_jalan')->nullable();
                $table->string('rt', 8)->nullable();
                $table->string('rw', 8)->nullable();
                $table->string('desa_kelurahan', 191)->nullable();
                $table->string('kecamatan', 191)->nullable();
                $table->string('kabupaten_kota', 191)->nullable();
                $table->string('provinsi', 191)->nullable();
                $table->string('kodepos', 16)->nullable();

                $table->string('foto_papan_nama_path')->nullable();
                $table->string('foto_gedung_path')->nullable();
                $table->string('foto_kelas_path')->nullable();
                $table->string('foto_halaman_path')->nullable();
                $table->string('foto_denah_path')->nullable();

                $table->string('operator_name');
                $table->string('operator_email');

                $table->string('mou_nomor_lp', 191)->nullable();
                $table->string('mou_nomor_sekolah', 191)->nullable();
                $table->timestamp('mou_signed_at')->nullable();
                $table->string('signature_path')->nullable();
                $table->string('materai_path')->nullable();

                $table->text('admin_notes')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->timestamp('approved_at')->nullable();

                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lembaga_registration_permits')) {
            Schema::create('lembaga_registration_permits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lembaga_registration_id')->constrained('lembaga_registrations')->cascadeOnDelete();
                $table->unsignedTinyInteger('sort_order')->default(0);
                $table->string('permit_key', 64);
                $table->string('nama_sk');
                $table->string('nomor_sk', 255)->nullable();
                $table->date('tanggal_sk')->nullable();
                $table->string('dokumen_path')->nullable();
                $table->timestamps();

                // Nama indeks eksplisit (<64 karakter) — default Laravel melebihi batas MySQL.
                $table->unique(['lembaga_registration_id', 'permit_key'], 'lr_permits_reg_id_key_uq');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lembaga_registration_permits');
        Schema::dropIfExists('lembaga_registrations');
    }
};
