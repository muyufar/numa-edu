<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_alumni_list(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $kelas = Kelas::query()->create([
            'sekolah_id' => 1,
            'tingkat' => 9,
            'nama' => 'A',
            'tahun_ajaran' => '2024/2025',
            'is_active' => false,
        ]);

        Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'ALU-001',
            'nama' => 'Budi Alumni',
            'status' => 'Lulus',
        ]);

        Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'AKT-001',
            'nama' => 'Siti Aktif',
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin)
            ->get(route('siswa.alumni.index'))
            ->assertOk()
            ->assertSee('Budi Alumni')
            ->assertDontSee('Siti Aktif');
    }
}
