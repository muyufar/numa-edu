<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Support\SidebarNavigation;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_wali_sees_modul_section_for_materi_only(): void
    {
        $this->seed(RoleSeeder::class);

        $wali = User::factory()->create(['sekolah_id' => 1]);
        $wali->assignRole('wali');

        $this->actingAs($wali);

        $this->assertTrue(SidebarNavigation::showModulSection());
        $this->assertTrue($wali->can('viewAny', \App\Models\MateriAjar::class));
        $this->assertFalse($wali->can('viewAny', \App\Models\Kelas::class));
        $this->assertFalse($wali->can('viewAny', \App\Models\Tagihan::class));
    }

    public function test_siswa_portal_hides_modul_section(): void
    {
        $this->seed(RoleSeeder::class);

        $siswaUser = User::factory()->create(['sekolah_id' => 1]);
        $siswaUser->assignRole('siswa');

        $this->actingAs($siswaUser);

        $this->assertTrue(SidebarNavigation::isSiswaPortalUser());
        $this->assertFalse(SidebarNavigation::showModulSection());
        $this->assertTrue($siswaUser->can('viewAny', \App\Models\MateriAjar::class));
        $this->assertFalse($siswaUser->can('viewAny', \App\Models\Nilai::class));
    }

    public function test_admin_sees_modul_section(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $this->assertTrue(SidebarNavigation::showModulSection());
        $this->assertTrue($admin->can('viewAny', \App\Models\Kelas::class));
        $this->assertTrue($admin->can('viewAny', \App\Models\Tagihan::class));
    }

    public function test_wali_sidebar_hides_staff_menus_in_html(): void
    {
        $this->seed(RoleSeeder::class);

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
            'nis' => 'NIS-SB-001',
            'nama' => 'Anak Sidebar',
        ]);

        $wali = User::factory()->create(['sekolah_id' => 1]);
        $wali->assignRole('wali');
        $wali->waliSiswas()->attach($siswa->id, ['hubungan' => 'ayah']);

        $response = $this->actingAs($wali)
            ->get(route('wali.index'));

        $response->assertOk()
            ->assertSee(__('Anak Saya'))
            ->assertSee(__('Kurikulum'));

        $this->assertFalse($wali->can('viewAny', \App\Models\Kelas::class));
        $this->assertFalse($wali->can('viewAny', \App\Models\Siswa::class));
    }
}
