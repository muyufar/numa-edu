<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KelasWaliKelasTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_wali_kelas_when_creating_kelas(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '33334444',
            'nama' => 'SMP Tes',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['sekolah_id' => $sekolah->id]);

        $guru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $guruUser->id,
            'nama' => 'Bu Wali',
            'nip' => '19800101',
        ]);

        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('kelas.store'), [
                'tingkat' => 7,
                'nama' => 'A',
                'tahun_ajaran' => '2026/2027',
                'wali_kelas_id' => (string) $guru->id,
                'is_active' => '1',
            ])
            ->assertRedirect(route('kelas.index'))
            ->assertSessionHas('status');

        $kelas = Kelas::withoutGlobalScopes()->first();
        $this->assertNotNull($kelas);
        $this->assertSame((int) $guru->id, (int) $kelas->wali_kelas_id);
    }

    public function test_admin_can_update_wali_kelas(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '44445555',
            'nama' => 'SMP Dua',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);

        $kelas = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 8,
            'nama' => 'B',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['sekolah_id' => $sekolah->id]);

        $guru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $guruUser->id,
            'nama' => 'Pak Mapel',
        ]);

        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->put(route('kelas.update', $kelas), [
                'tingkat' => 8,
                'nama' => 'B',
                'tahun_ajaran' => '2026/2027',
                'wali_kelas_id' => (string) $guru->id,
                'is_active' => '1',
            ])
            ->assertRedirect(route('kelas.index'));

        $this->assertSame((int) $guru->id, (int) $kelas->fresh()->wali_kelas_id);
    }

    public function test_kelas_index_shows_wali_kelas_name(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '55556666',
            'nama' => 'SMP Tiga',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $guru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $guruUser->id,
            'nama' => 'Ibu Siti Wali',
        ]);

        Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 9,
            'nama' => 'C',
            'tahun_ajaran' => '2026/2027',
            'wali_kelas_id' => $guru->id,
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('kelas.index'))
            ->assertOk()
            ->assertSee('Ibu Siti Wali');
    }

    public function test_cannot_assign_guru_from_different_sekolah_as_wali_kelas(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolahA = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '66667777',
            'nama' => 'SMP A',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);
        $sekolahB = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '77778888',
            'nama' => 'SMP B',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);

        $otherGuruUser = User::factory()->create(['sekolah_id' => $sekolahB->id]);
        $otherGuru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolahB->id,
            'user_id' => $otherGuruUser->id,
            'nama' => 'Guru Lain Sekolah',
        ]);

        $admin = User::factory()->create(['sekolah_id' => $sekolahA->id]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('kelas.store'), [
                'tingkat' => 7,
                'nama' => 'A',
                'tahun_ajaran' => '2026/2027',
                'wali_kelas_id' => (string) $otherGuru->id,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('wali_kelas_id');

        $this->assertSame(0, Kelas::withoutGlobalScopes()->count());
    }
}
