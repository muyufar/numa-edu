<?php

namespace Tests\Feature;

use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaAkunGlobalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sekolah_only_sees_students_in_own_sekolah(): void
    {
        $this->seed(RoleSeeder::class);

        $school2 = Sekolah::withoutGlobalScopes()->create([
            'cabang_id' => 1,
            'npsn' => '44444444',
            'nama' => 'Sekolah 4',
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $s1 = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'user_id' => null,
            'kelas_id' => null,
            'nis' => 'NIS-G1',
            'nama' => 'Siswa Sekolah 1',
            'tanggal_lahir' => '2011-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => null,
        ]);

        $s2 = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $school2->id,
            'user_id' => null,
            'kelas_id' => null,
            'nis' => 'NIS-G2',
            'nama' => 'Siswa Sekolah 2',
            'tanggal_lahir' => '2011-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => null,
        ]);

        $this->actingAs($admin);

        $res = $this->get(route('siswa-akun-admin.index'));
        $res->assertOk();
        $res->assertSee($s1->nama);
        $res->assertDontSee($s2->nama);
    }

    public function test_super_admin_can_filter_by_sekolah(): void
    {
        $this->seed(RoleSeeder::class);

        $school2 = Sekolah::withoutGlobalScopes()->create([
            'cabang_id' => 1,
            'npsn' => '55555555',
            'nama' => 'Sekolah 5',
            'is_active' => true,
        ]);

        $super = User::factory()->create(['sekolah_id' => null]);
        $super->assignRole('super_admin');

        $s1 = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'user_id' => null,
            'kelas_id' => null,
            'nis' => 'NIS-SA1',
            'nama' => 'Siswa A',
            'tanggal_lahir' => '2011-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => null,
        ]);

        $s2 = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $school2->id,
            'user_id' => null,
            'kelas_id' => null,
            'nis' => 'NIS-SA2',
            'nama' => 'Siswa B',
            'tanggal_lahir' => '2011-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => null,
        ]);

        $this->actingAs($super);

        $res = $this->get(route('siswa-akun-admin.index', ['sekolah_id' => 1]));
        $res->assertOk();
        $res->assertSee($s1->nama);
        $res->assertDontSee($s2->nama);
    }
}

