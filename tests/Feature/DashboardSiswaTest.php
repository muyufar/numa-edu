<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Perizinan;
use App\Models\PresensiSiswa;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_siswa_dashboard_shows_jadwal_presensi_and_perizinan(): void
    {
        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '10101010',
            'nama' => 'SMP Portal',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);

        $kelas = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 7,
            'nama' => 'A',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $mapel = MataPelajaran::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kode' => 'MTK',
            'nama' => 'Matematika',
        ]);

        $guruUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $guru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $guruUser->id,
            'nama' => 'Pak Guru MTK',
        ]);

        Jadwal::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $guru->id,
            'hari' => 'Selasa',
            'jam_mulai' => '07:50',
            'jam_selesai' => '09:50',
            'tahun_ajaran' => '2026/2027',
        ]);

        $siswaUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $siswaUser->assignRole('siswa');

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $siswaUser->id,
            'kelas_id' => $kelas->id,
            'nis' => '20261001',
            'nama' => 'Budi Siswa',
        ]);

        PresensiSiswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'siswa_id' => $siswa->id,
            'tanggal' => now()->toDateString(),
            'status' => 'hadir',
            'metode' => 'manual',
            'presensi_slot' => 'harian',
        ]);

        Perizinan::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'siswa_id' => $siswa->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'izin',
            'keterangan' => 'Acara keluarga',
            'status' => 'pending',
            'diajukan_oleh' => $siswaUser->id,
        ]);

        $this->actingAs($siswaUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard siswa')
            ->assertSee('Matematika')
            ->assertSee('Pak Guru MTK')
            ->assertSee('Selasa')
            ->assertSee('Hadir')
            ->assertSee('Acara keluarga')
            ->assertSee('Menunggu')
            ->assertDontSee('Ringkasan sekolah');
    }

    public function test_siswa_dashboard_hides_admin_modul_sidebar(): void
    {
        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '20202020',
            'nama' => 'SMP Sidebar',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);

        $siswaUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $siswaUser->assignRole('siswa');

        Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $siswaUser->id,
            'nis' => '20261002',
            'nama' => 'Ani Siswa',
        ]);

        $this->actingAs($siswaUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(__('Modul'))
            ->assertSee(__('Presensi saya'));
    }
}
