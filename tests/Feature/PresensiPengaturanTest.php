<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\PresensiSiswa;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use App\Support\SekolahPresensiSettings;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresensiPengaturanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_set_presensi_mode_per_mapel(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->put(route('pengaturan.presensi.update'), [
                'presensi_siswa_mode' => SekolahPresensiSettings::MODE_PER_MAPEL,
            ])
            ->assertRedirect(route('pengaturan.presensi.edit'));

        $this->assertSame(
            SekolahPresensiSettings::MODE_PER_MAPEL,
            Sekolah::query()->find(1)?->presensi_siswa_mode
        );
    }

    public function test_per_mapel_presensi_stores_jadwal_slot(): void
    {
        $this->seed(RoleSeeder::class);

        Sekolah::query()->whereKey(1)->update([
            'presensi_siswa_mode' => SekolahPresensiSettings::MODE_PER_MAPEL,
        ]);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $kelas = Kelas::query()->create([
            'tingkat' => 8,
            'nama' => 'MapelTest',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $mapel = MataPelajaran::query()->create(['nama' => 'Matematika', 'kode' => 'MTK']);

        $guruUser = User::factory()->create(['sekolah_id' => 1]);
        $guruUser->assignRole('guru');

        $guru = \App\Models\Guru::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'user_id' => $guruUser->id,
            'nip' => 'NIP-J-'.uniqid(),
            'nama' => 'Guru Mapel',
        ]);

        $jadwal = Jadwal::query()->create([
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $guru->id,
            'hari' => Jadwal::hariFromDate(now()->toDateString()),
            'jam_mulai' => '07:30',
            'jam_selesai' => '08:30',
            'tahun_ajaran' => '2025/2026',
        ]);

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-MAPEL-'.uniqid(),
            'nama' => 'Siswa Mapel',
        ]);

        $tanggal = now()->toDateString();

        $this->actingAs($admin)
            ->post(route('presensi.siswa.store'), [
                'kelas_id' => $kelas->id,
                'jadwal_id' => $jadwal->id,
                'tanggal' => $tanggal,
                'presensi' => [
                    ['siswa_id' => $siswa->id, 'status' => 'hadir', 'keterangan' => null],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('presensi_siswas', [
            'siswa_id' => $siswa->id,
            'jadwal_id' => $jadwal->id,
            'presensi_slot' => 'j:'.$jadwal->id,
            'status' => 'hadir',
        ]);

        $count = PresensiSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $tanggal)
            ->count();

        $this->assertSame(1, $count);
    }
}
