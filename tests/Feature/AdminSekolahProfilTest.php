<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminSekolahProfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_update_own_school_profile(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '12345678',
            'nama' => 'MI Satu',
            'jenjang' => 'mi',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'sekolah_id' => $sekolah->id,
        ]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('profil-lembaga.edit'))
            ->assertOk()
            ->assertSee('MI Satu', false);

        $this->actingAs($admin)->put(route('profil-lembaga.update'), [
            'nama' => 'MI Satu Diperbarui',
            'jenjang' => 'sd',
            'alamat' => 'Jl. Test',
        ])->assertRedirect(route('profil-lembaga.edit'));

        $this->assertSame('MI Satu Diperbarui', $sekolah->fresh()->nama);
        $this->assertSame('sd', $sekolah->fresh()->jenjang);
        $this->assertTrue($sekolah->fresh()->is_active);
    }

    public function test_guru_cannot_access_admin_school_profile_route(): void
    {
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '87654321',
            'nama' => 'SD Dua',
            'jenjang' => 'sd',
            'is_active' => true,
        ]);

        $guru = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $guru->assignRole('guru');

        $this->actingAs($guru)
            ->get(route('profil-lembaga.edit'))
            ->assertForbidden();
    }
}
