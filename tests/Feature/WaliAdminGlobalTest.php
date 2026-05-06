<?php

namespace Tests\Feature;

use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliAdminGlobalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sekolah_can_create_wali_from_global_page(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $this->get(route('wali-admin.create'))->assertOk();

        $res = $this->post(route('wali-admin.store'), [
            'name' => 'Wali Baru',
            'email' => 'walibaru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'walibaru@example.com',
            'jenis_akun' => 'wali',
            'sekolah_id' => 1,
        ]);
    }

    public function test_admin_sekolah_can_update_wali_profile_and_reset_password(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $wali = User::factory()->create(['sekolah_id' => 1, 'email' => 'waliupd@example.com']);
        $wali->assignRole('wali');

        $this->actingAs($admin);

        $this->put(route('wali-admin.update', $wali), [
            'name' => 'Wali Update',
            'email' => 'waliupd2@example.com',
            'phone' => '08123456789',
        ])->assertRedirect(route('wali-admin.show', $wali));

        $this->assertDatabaseHas('users', [
            'id' => $wali->id,
            'email' => 'waliupd2@example.com',
            'phone' => '08123456789',
        ]);

        $this->post(route('wali-admin.reset-password', $wali), [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('wali-admin.show', $wali));
    }

    public function test_admin_sekolah_can_attach_child_from_wali_detail(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $wali = User::factory()->create(['sekolah_id' => 1]);
        $wali->assignRole('wali');

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'user_id' => null,
            'kelas_id' => null,
            'nis' => 'NIS-WALI-'.uniqid(),
            'nama' => 'Siswa Anak',
            'tanggal_lahir' => '2012-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => null,
        ]);

        $this->actingAs($admin);

        $this->post(route('wali-admin.attach-siswa', $wali), [
            'siswa_id' => $siswa->id,
            'hubungan' => 'ayah',
        ])->assertRedirect(route('wali-admin.show', $wali));

        $this->assertTrue($wali->fresh()->waliSiswas()->where('siswas.id', $siswa->id)->exists());
    }

    public function test_admin_sekolah_only_sees_wali_in_own_sekolah(): void
    {
        $this->seed(RoleSeeder::class);

        $school2 = Sekolah::withoutGlobalScopes()->create([
            'cabang_id' => 1,
            'npsn' => '22222222',
            'nama' => 'Sekolah 2',
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $wali1 = User::factory()->create(['sekolah_id' => 1, 'email' => 'wali1@example.com']);
        $wali1->assignRole('wali');

        $wali2 = User::factory()->create(['sekolah_id' => $school2->id, 'email' => 'wali2@example.com']);
        $wali2->assignRole('wali');

        $this->actingAs($admin);

        $res = $this->get(route('wali-admin.index'));
        $res->assertOk();
        $res->assertSee('wali1@example.com');
        $res->assertDontSee('wali2@example.com');
    }

    public function test_super_admin_can_see_all_wali(): void
    {
        $this->seed(RoleSeeder::class);

        $school2 = Sekolah::withoutGlobalScopes()->create([
            'cabang_id' => 1,
            'npsn' => '33333333',
            'nama' => 'Sekolah 3',
            'is_active' => true,
        ]);

        $super = User::factory()->create(['sekolah_id' => null]);
        $super->assignRole('super_admin');

        $wali1 = User::factory()->create(['sekolah_id' => 1, 'email' => 'wali11@example.com']);
        $wali1->assignRole('wali');

        $wali2 = User::factory()->create(['sekolah_id' => $school2->id, 'email' => 'wali22@example.com']);
        $wali2->assignRole('wali');

        $this->actingAs($super);

        $res = $this->get(route('wali-admin.index'));
        $res->assertOk();
        $res->assertSee('wali11@example.com');
        $res->assertSee('wali22@example.com');
    }
}

