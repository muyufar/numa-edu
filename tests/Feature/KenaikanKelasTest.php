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

class KenaikanKelasTest extends TestCase
{
    use RefreshDatabase;

    private function seedAdminForSekolah(Sekolah $sekolah): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        return $admin;
    }

    private function makeSekolah(): Sekolah
    {
        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);

        return Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '99998888',
            'nama' => 'SMP Tes',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_kenaikan_kelas_page(): void
    {
        $sekolah = $this->makeSekolah();
        $admin = $this->seedAdminForSekolah($sekolah);

        $this->actingAs($admin)
            ->get(route('siswa.kenaikan-kelas.index'))
            ->assertOk()
            ->assertSee(__('Kenaikan kelas & kelulusan'));
    }

    public function test_admin_can_promote_students_to_next_class(): void
    {
        $sekolah = $this->makeSekolah();
        $admin = $this->seedAdminForSekolah($sekolah);

        $kelas7 = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 7,
            'nama' => 'A',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $kelas8 = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 8,
            'nama' => 'A',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $siswa1 = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas7->id,
            'nis' => 'KNAIK-01',
            'nama' => 'Rina Naik',
            'status' => 'Aktif',
        ]);

        $siswa2 = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas7->id,
            'nis' => 'KNAIK-02',
            'nama' => 'Budi Naik',
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin)
            ->post(route('siswa.kenaikan-kelas.naik'), [
                'kelas_asal_id' => $kelas7->id,
                'kelas_tujuan_id' => $kelas8->id,
                'siswa_ids' => [$siswa1->id, $siswa2->id],
            ])
            ->assertRedirect(route('siswa.kenaikan-kelas.index', ['kelas_asal_id' => $kelas7->id]))
            ->assertSessionHas('status');

        $this->assertSame((int) $kelas8->id, (int) $siswa1->fresh()->kelas_id);
        $this->assertSame((int) $kelas8->id, (int) $siswa2->fresh()->kelas_id);
        $this->assertSame('8 A', $siswa1->fresh()->tingkat_rombel);
    }

    public function test_admin_can_graduate_students_to_alumni(): void
    {
        $sekolah = $this->makeSekolah();
        $admin = $this->seedAdminForSekolah($sekolah);

        $kelas9 = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 9,
            'nama' => 'B',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas9->id,
            'nis' => 'LULUS-01',
            'nama' => 'Andi Lulus',
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin)
            ->post(route('siswa.kenaikan-kelas.luluskan'), [
                'kelas_id' => $kelas9->id,
                'siswa_ids' => [$siswa->id],
                'status' => 'Lulus',
            ])
            ->assertRedirect(route('siswa.alumni.index'))
            ->assertSessionHas('status');

        $siswa->refresh();
        $this->assertSame('Lulus', $siswa->status);
        $this->assertTrue($siswa->isAlumni());
        $this->assertSame((int) $kelas9->id, (int) $siswa->kelas_id);

        $this->actingAs($admin)
            ->get(route('siswa.alumni.index'))
            ->assertOk()
            ->assertSee('Andi Lulus');
    }

    public function test_cannot_promote_with_same_source_and_target_class(): void
    {
        $sekolah = $this->makeSekolah();
        $admin = $this->seedAdminForSekolah($sekolah);

        $kelas = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 7,
            'nama' => 'A',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'nis' => 'SAMA-01',
            'nama' => 'Tes Sama',
        ]);

        $this->actingAs($admin)
            ->from(route('siswa.kenaikan-kelas.index', ['kelas_asal_id' => $kelas->id]))
            ->post(route('siswa.kenaikan-kelas.naik'), [
                'kelas_asal_id' => $kelas->id,
                'kelas_tujuan_id' => $kelas->id,
                'siswa_ids' => [$siswa->id],
            ])
            ->assertSessionHasErrors('kelas_tujuan_id');
    }

    public function test_guru_cannot_access_kenaikan_kelas_page(): void
    {
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);

        $sekolah = $this->makeSekolah();
        $guru = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $guru->assignRole('guru');

        $this->actingAs($guru)
            ->get(route('siswa.kenaikan-kelas.index'))
            ->assertForbidden();
    }
}
