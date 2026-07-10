<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return list<string>
     */
    private function sharedGtkColumns(): array
    {
        return [
            'nik', 'nuptk', 'status_kepegawaian', 'tempat_lahir', 'tanggal_lahir',
            'agama', 'nama_ibu_kandung', 'status_perkawinan', 'email_pribadi',
            'kewarganegaraan', 'alamat_jalan', 'rt_rw', 'kode_pos', 'dusun',
            'desa_kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi',
            'telepon_rumah', 'jenis_ptk', 'sk_pengangkatan', 'tmt_cpns',
            'tmt_pns', 'tmt_jabatan',
        ];
    }

    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table): void {
            $table->string('nik', 32)->nullable();
            $table->string('nuptk', 32)->nullable();
            $table->string('status_kepegawaian', 64)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 32)->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('status_perkawinan', 32)->nullable();
            $table->string('email_pribadi')->nullable();
            $table->string('kewarganegaraan', 64)->nullable();
            $table->text('alamat_jalan')->nullable();
            $table->string('rt_rw', 16)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('dusun')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('telepon_rumah', 20)->nullable();
            $table->string('jenis_ptk', 64)->nullable();
            $table->string('sk_pengangkatan')->nullable();
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();
            $table->date('tmt_jabatan')->nullable();
            $table->string('tugas', 128)->nullable();
            $table->string('mata_pelajaran')->nullable();
            $table->string('penempatan')->nullable();
            $table->string('total_jtm', 16)->nullable();
        });

        Schema::table('pegawais', function (Blueprint $table): void {
            $table->string('nik', 32)->nullable();
            $table->string('nuptk', 32)->nullable();
            $table->string('status_kepegawaian', 64)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 32)->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('status_perkawinan', 32)->nullable();
            $table->string('email_pribadi')->nullable();
            $table->string('kewarganegaraan', 64)->nullable();
            $table->text('alamat_jalan')->nullable();
            $table->string('rt_rw', 16)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('dusun')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('telepon_rumah', 20)->nullable();
            $table->string('jenis_ptk', 64)->nullable();
            $table->string('sk_pengangkatan')->nullable();
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();
            $table->date('tmt_jabatan')->nullable();
            $table->string('jenis_kelamin', 1)->nullable();
            $table->string('phone', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table): void {
            $table->dropColumn(array_merge($this->sharedGtkColumns(), [
                'tugas', 'mata_pelajaran', 'penempatan', 'total_jtm',
            ]));
        });

        Schema::table('pegawais', function (Blueprint $table): void {
            $table->dropColumn(array_merge($this->sharedGtkColumns(), [
                'jenis_kelamin', 'phone',
            ]));
        });
    }
};
