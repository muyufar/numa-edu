<?php

namespace Tests\Feature\PengurusCabang;

use App\Models\Cabang;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SekolahProfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_school_profile(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC A', 'kode' => 'A']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '11112222',
            'nama' => 'MI Lama',
            'jenjang' => 'mi',
            'is_active' => true,
        ]);

        $super = User::factory()->create();
        $super->assignRole('super_admin');

        $response = $this->actingAs($super)->put(route('pengurus.sekolah.update', $sekolah), [
            'nama' => 'MI Baru',
            'jenjang' => 'sd',
            'alamat' => 'Jl. Contoh',
            'kode_provinsi' => '31',
            'nama_provinsi' => 'DKI Jakarta',
            'kode_kabupaten' => '31.74',
            'nama_kabupaten' => 'Jakarta Selatan',
            'alamat_dusun' => 'Dusun A, RT 01/RW 02',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('pengurus.sekolah.index'));
        $fresh = $sekolah->fresh();
        $this->assertSame('MI Baru', $fresh->nama);
        $this->assertSame('sd', $fresh->jenjang);
        $this->assertSame('Jl. Contoh', $fresh->alamat);
        $this->assertSame('31', $fresh->kode_provinsi);
        $this->assertSame('DKI Jakarta', $fresh->nama_provinsi);
        $this->assertSame('31.74', $fresh->kode_kabupaten);
        $this->assertSame('Jakarta Selatan', $fresh->nama_kabupaten);
        $this->assertSame('Dusun A, RT 01/RW 02', $fresh->alamat_dusun);
    }

    public function test_pengurus_cannot_update_school_from_other_cabang(): void
    {
        Role::firstOrCreate(['name' => 'pengurus_cabang', 'guard_name' => 'web']);

        $cabangA = Cabang::query()->create(['nama' => 'PC A', 'kode' => 'A']);
        $cabangB = Cabang::query()->create(['nama' => 'PC B', 'kode' => 'B']);

        $sekolahB = Sekolah::query()->create([
            'cabang_id' => $cabangB->id,
            'npsn' => '22223333',
            'nama' => 'MI Cabang B',
            'jenjang' => 'mts',
            'is_active' => true,
        ]);

        $pengurus = User::factory()->create(['cabang_id' => $cabangA->id]);
        $pengurus->assignRole('pengurus_cabang');

        $this->actingAs($pengurus)
            ->get(route('pengurus.sekolah.edit', $sekolahB))
            ->assertForbidden();
    }

    public function test_pengurus_can_update_school_in_own_cabang(): void
    {
        Role::firstOrCreate(['name' => 'pengurus_cabang', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC A', 'kode' => 'A']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '33334444',
            'nama' => 'MI Saya',
            'jenjang' => 'sd',
            'is_active' => true,
        ]);

        $pengurus = User::factory()->create(['cabang_id' => $cabang->id]);
        $pengurus->assignRole('pengurus_cabang');

        $this->actingAs($pengurus)
            ->put(route('pengurus.sekolah.update', $sekolah), [
                'nama' => 'MI Diperbarui',
                'jenjang' => 'sd',
                'is_active' => true,
            ])
            ->assertRedirect(route('pengurus.sekolah.index'));

        $this->assertSame('MI Diperbarui', $sekolah->fresh()->nama);
    }

    public function test_default_school_cannot_be_deactivated(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $defaultId = (int) config('tenancy.default_sekolah_id', 1);
        $sekolah = Sekolah::query()->findOrFail($defaultId);

        $super = User::factory()->create();
        $super->assignRole('super_admin');

        $this->actingAs($super)
            ->from(route('pengurus.sekolah.edit', $sekolah))
            ->put(route('pengurus.sekolah.update', $sekolah), [
                'nama' => $sekolah->nama,
                'jenjang' => $sekolah->jenjang ?? 'sd',
                'is_active' => false,
            ])
            ->assertSessionHasErrors('is_active');
    }
}
