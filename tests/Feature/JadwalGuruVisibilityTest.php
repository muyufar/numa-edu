<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Sekolah;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalGuruVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_guru_without_user_sekolah_id_can_see_own_jadwal(): void
    {
        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '88889999',
            'nama' => 'SMP Jadwal',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);

        $kelas = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 7,
            'nama' => 'A',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $mapel = MataPelajaran::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kode' => 'MTK',
            'nama' => 'Matematika',
        ]);

        $guruUser = User::factory()->create([
            'sekolah_id' => null,
            'name' => 'ADAM SUTEJO, S.Pd',
        ]);
        $guruUser->assignRole('guru');

        $guru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $guruUser->id,
            'nama' => 'ADAM SUTEJO, S.Pd',
        ]);

        $otherGuruUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $otherGuruUser->assignRole('guru');
        $otherGuru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $otherGuruUser->id,
            'nama' => 'Guru Lain',
        ]);

        Jadwal::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $guru->id,
            'hari' => 'Selasa',
            'jam_mulai' => '07:50',
            'jam_selesai' => '09:50',
            'tahun_ajaran' => '2026/2027',
        ]);

        Jadwal::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $otherGuru->id,
            'hari' => 'Rabu',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'tahun_ajaran' => '2026/2027',
        ]);

        $this->actingAs($guruUser)
            ->get(route('jadwal.index'))
            ->assertOk()
            ->assertSee('Matematika')
            ->assertSee('7 A')
            ->assertDontSee('Guru Lain');
    }

    public function test_admin_sees_all_jadwal_in_school(): void
    {
        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '99990000',
            'nama' => 'SMP Admin',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);

        $kelas = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 7,
            'nama' => 'A',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $mapel = MataPelajaran::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kode' => 'MTK',
            'nama' => 'Matematika',
        ]);

        $guruUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $guruUser->assignRole('guru');
        $guru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $guruUser->id,
            'nama' => 'ADAM SUTEJO, S.Pd',
        ]);

        $otherGuruUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $otherGuruUser->assignRole('guru');
        $otherGuru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $otherGuruUser->id,
            'nama' => 'Guru Lain',
        ]);

        Jadwal::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $guru->id,
            'hari' => 'Selasa',
            'jam_mulai' => '07:50',
            'jam_selesai' => '09:50',
            'tahun_ajaran' => '2026/2027',
        ]);

        Jadwal::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $otherGuru->id,
            'hari' => 'Rabu',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'tahun_ajaran' => '2026/2027',
        ]);

        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('jadwal.index'))
            ->assertOk()
            ->assertSee('ADAM SUTEJO, S.Pd')
            ->assertSee('Guru Lain');
    }
}
