<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KelasAttachSiswaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_attach_unassigned_siswa_to_kelas(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '11112222',
            'nama' => 'SD Tes',
            'jenjang' => 'sd',
            'is_active' => true,
        ]);

        $kelas = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 1,
            'nama' => 'A',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => null,
            'nis' => '20260001',
            'nama' => 'Ani',
        ]);

        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('kelas.siswa.attach', $kelas), [
                'siswa_ids' => [(string) $siswa->id],
            ])
            ->assertRedirect(route('kelas.edit', $kelas))
            ->assertSessionHas('status_siswa');

        $this->assertSame((int) $kelas->id, (int) $siswa->fresh()->kelas_id);
    }

    public function test_cannot_attach_siswa_already_in_a_class(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '22223333',
            'nama' => 'SD Dua',
            'jenjang' => 'sd',
            'is_active' => true,
        ]);

        $kelasA = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 1,
            'nama' => 'A',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $kelasB = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 1,
            'nama' => 'B',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelasA->id,
            'nis' => 'X1',
            'nama' => 'Sudah Ada Kelas',
        ]);

        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->from(route('kelas.edit', $kelasB))
            ->post(route('kelas.siswa.attach', $kelasB), [
                'siswa_ids' => [$siswa->id],
            ])
            ->assertRedirect(route('kelas.edit', $kelasB))
            ->assertSessionHasErrors('siswa_ids.0');

        $this->assertSame((int) $kelasA->id, (int) $siswa->fresh()->kelas_id);
    }
}
