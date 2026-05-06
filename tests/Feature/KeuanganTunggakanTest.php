<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganTunggakanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_tunggakan_and_export(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $kelas = Kelas::query()->create([
            'sekolah_id' => 1,
            'tingkat' => 1,
            'nama' => 'A',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $siswa = Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-TGG',
            'nama' => 'Siswa Tunggakan',
        ]);

        Tagihan::query()->create([
            'siswa_id' => $siswa->id,
            'jenis' => 'SPP',
            'periode' => '2026-05',
            'jumlah' => 200000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($admin)
            ->get(route('keuangan.tunggakan.index', ['periode_from' => '2026-05', 'periode_to' => '2026-05']))
            ->assertOk()
            ->assertSee('Tunggakan')
            ->assertSee('Siswa Tunggakan');

        $this->actingAs($admin)
            ->get(route('keuangan.tunggakan.export', ['periode_from' => '2026-05', 'periode_to' => '2026-05']))
            ->assertOk();
    }
}
