<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\PresensiSiswa;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresensiSiswaAksesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function seedSekolah(): Sekolah
    {
        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);

        return Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '88889999',
            'nama' => 'SMP Test',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);
    }

    public function test_siswa_can_view_own_presensi_history(): void
    {
        $sekolah = $this->seedSekolah();
        $kelas = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 7,
            'nama' => 'A',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $siswaUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $siswaUser->assignRole('siswa');

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'user_id' => $siswaUser->id,
            'nama' => 'Budi Siswa',
            'nis' => '1001',
        ]);

        $lain = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'nama' => 'Ani Siswa',
            'nis' => '1002',
        ]);

        PresensiSiswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'siswa_id' => $siswa->id,
            'tanggal' => now()->toDateString(),
            'presensi_slot' => 'harian',
            'status' => 'hadir',
            'metode' => 'barcode',
        ]);

        PresensiSiswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'siswa_id' => $lain->id,
            'tanggal' => now()->toDateString(),
            'presensi_slot' => 'harian',
            'status' => 'hadir',
            'metode' => 'manual',
        ]);

        $this->actingAs($siswaUser)
            ->get(route('presensi.siswa.index'))
            ->assertOk()
            ->assertSee(__('Presensi saya'), false)
            ->assertSee(__('Hadir'), false)
            ->assertDontSee('Ani Siswa', false);
    }

    public function test_siswa_cannot_access_presensi_input_page(): void
    {
        $sekolah = $this->seedSekolah();
        $siswaUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $siswaUser->assignRole('siswa');

        Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $siswaUser->id,
            'nama' => 'Budi Siswa',
            'nis' => '1001',
        ]);

        $this->actingAs($siswaUser)
            ->get(route('presensi.siswa.create'))
            ->assertForbidden();
    }
}
