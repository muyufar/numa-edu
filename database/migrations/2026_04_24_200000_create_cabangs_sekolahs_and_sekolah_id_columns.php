<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode', 32)->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->nullOnDelete();
            $table->string('npsn', 16)->unique();
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('telepon', 32)->nullable();
            $table->string('email_kantor')->nullable();
            $table->string('website')->nullable();
            $table->string('kepala_nama')->nullable();
            $table->string('kepala_nip', 32)->nullable();
            $table->string('akreditasi', 8)->nullable();
            $table->string('akreditasi_tahun', 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('cabangs')->insert([
            'nama' => 'Cabang Default',
            'kode' => 'DEFAULT',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sekolahs')->insert([
            'cabang_id' => 1,
            'npsn' => '00000000',
            'nama' => config('app.name', 'Sekolah Default'),
            'alamat' => null,
            'telepon' => null,
            'email_kantor' => null,
            'website' => null,
            'kepala_nama' => null,
            'kepala_nip' => null,
            'akreditasi' => null,
            'akreditasi_tahun' => null,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $defaultSekolahId = 1;

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->after('id')->constrained('cabangs')->nullOnDelete();
            $table->foreignId('sekolah_id')->nullable()->after('cabang_id')->constrained('sekolahs')->nullOnDelete();
        });

        $superIds = collect();
        if (Schema::hasTable('model_has_roles') && Schema::hasTable('roles')) {
            $superIds = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'super_admin')
                ->where('model_has_roles.model_type', \App\Models\User::class)
                ->pluck('model_has_roles.model_id');
        }

        DB::table('users')->whereNotIn('id', $superIds)->update(['sekolah_id' => $defaultSekolahId]);

        $tenantTables = [
            'kelas',
            'siswas',
            'gurus',
            'pegawais',
            'mata_pelajarans',
            'jadwals',
            'nilais',
            'tagihans',
            'pembayarans',
            'ppdb_registrations',
            'beritas',
            'perizinans',
            'pelanggarans',
            'inventaris_kategoris',
            'inventaris_barangs',
            'inventaris_mutasis',
            'presensi_siswas',
            'presensi_gurus',
            'presensi_pegawais',
            'kinerja_penilaians',
            'materi_ajars',
            'kurikulum_items',
        ];

        foreach ($tenantTables as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->foreignId('sekolah_id')->nullable()->after('id')->constrained('sekolahs');
            });
        }

        DB::table('kelas')->update(['sekolah_id' => $defaultSekolahId]);

        DB::statement('UPDATE siswas SET sekolah_id = (SELECT k.sekolah_id FROM kelas AS k WHERE k.id = siswas.kelas_id) WHERE kelas_id IS NOT NULL');
        DB::table('siswas')->whereNull('sekolah_id')->update(['sekolah_id' => $defaultSekolahId]);

        DB::table('gurus')->update(['sekolah_id' => $defaultSekolahId]);
        DB::table('pegawais')->update(['sekolah_id' => $defaultSekolahId]);
        DB::table('mata_pelajarans')->update(['sekolah_id' => $defaultSekolahId]);

        DB::statement('UPDATE jadwals SET sekolah_id = (SELECT k.sekolah_id FROM kelas AS k WHERE k.id = jadwals.kelas_id)');

        DB::statement('UPDATE nilais SET sekolah_id = (SELECT s.sekolah_id FROM siswas AS s WHERE s.id = nilais.siswa_id) WHERE siswa_id IS NOT NULL');
        DB::statement('UPDATE nilais SET sekolah_id = (SELECT k.sekolah_id FROM kelas AS k WHERE k.id = nilais.kelas_id) WHERE sekolah_id IS NULL');

        DB::statement('UPDATE tagihans SET sekolah_id = (SELECT s.sekolah_id FROM siswas AS s WHERE s.id = tagihans.siswa_id)');

        DB::statement('UPDATE pembayarans SET sekolah_id = (SELECT t.sekolah_id FROM tagihans AS t WHERE t.id = pembayarans.tagihan_id)');

        DB::table('ppdb_registrations')->update(['sekolah_id' => $defaultSekolahId]);
        DB::table('beritas')->update(['sekolah_id' => $defaultSekolahId]);

        DB::statement('UPDATE perizinans SET sekolah_id = (SELECT s.sekolah_id FROM siswas AS s WHERE s.id = perizinans.siswa_id)');
        DB::statement('UPDATE pelanggarans SET sekolah_id = (SELECT s.sekolah_id FROM siswas AS s WHERE s.id = pelanggarans.siswa_id)');

        DB::table('inventaris_kategoris')->update(['sekolah_id' => $defaultSekolahId]);
        DB::table('inventaris_barangs')->update(['sekolah_id' => $defaultSekolahId]);

        DB::statement('UPDATE inventaris_mutasis SET sekolah_id = (SELECT b.sekolah_id FROM inventaris_barangs AS b WHERE b.id = inventaris_mutasis.inventaris_barang_id)');

        DB::statement('UPDATE presensi_siswas SET sekolah_id = (SELECT s.sekolah_id FROM siswas AS s WHERE s.id = presensi_siswas.siswa_id)');
        DB::statement('UPDATE presensi_gurus SET sekolah_id = (SELECT g.sekolah_id FROM gurus AS g WHERE g.id = presensi_gurus.guru_id)');
        DB::statement('UPDATE presensi_pegawais SET sekolah_id = (SELECT p.sekolah_id FROM pegawais AS p WHERE p.id = presensi_pegawais.pegawai_id)');

        DB::table('kinerja_penilaians')->update(['sekolah_id' => $defaultSekolahId]);
        DB::table('materi_ajars')->update(['sekolah_id' => $defaultSekolahId]);
        DB::table('kurikulum_items')->update(['sekolah_id' => $defaultSekolahId]);
    }

    public function down(): void
    {
        $tenantTables = [
            'kurikulum_items',
            'materi_ajars',
            'kinerja_penilaians',
            'presensi_pegawais',
            'presensi_gurus',
            'presensi_siswas',
            'inventaris_mutasis',
            'inventaris_barangs',
            'inventaris_kategoris',
            'pelanggarans',
            'perizinans',
            'beritas',
            'ppdb_registrations',
            'pembayarans',
            'tagihans',
            'nilais',
            'jadwals',
            'mata_pelajarans',
            'pegawais',
            'gurus',
            'siswas',
            'kelas',
        ];

        foreach ($tenantTables as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->dropConstrainedForeignId('sekolah_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sekolah_id');
            $table->dropConstrainedForeignId('cabang_id');
        });

        Schema::dropIfExists('sekolahs');
        Schema::dropIfExists('cabangs');
    }
};
