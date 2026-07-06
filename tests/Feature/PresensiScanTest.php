<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Services\PresensiScanService;
use App\Support\FaceDescriptorMatcher;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresensiScanTest extends TestCase
{
    use RefreshDatabase;

    private function seedSiswa(): Siswa
    {
        $kelas = Kelas::query()->create([
            'tingkat' => 7,
            'nama' => 'ScanTest',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        return Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-SCAN-'.uniqid(),
            'nama' => 'Siswa Scan',
            'presensi_kode' => 'NUMA-SIS-TESTCODE001',
        ]);
    }

    public function test_barcode_scan_records_hadir_for_siswa(): void
    {
        $this->seed(RoleSeeder::class);
        $siswa = $this->seedSiswa();

        $service = app(PresensiScanService::class);
        $result = $service->recordBarcode('siswa', $siswa->presensi_kode);

        $this->assertTrue($result['ok']);
        $this->assertSame('Siswa Scan', $result['nama']);
        $this->assertDatabaseHas('presensi_siswas', [
            'siswa_id' => $siswa->id,
            'status' => 'hadir',
            'metode' => 'barcode',
        ]);
    }

    public function test_face_match_records_hadir_for_guru(): void
    {
        $this->seed(RoleSeeder::class);

        $descriptor = array_fill(0, 128, 0.12);

        $user = User::factory()->create();
        $user->assignRole('siswa');

        $guru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'user_id' => $user->id,
            'nip' => 'NIP-'.uniqid(),
            'nama' => 'Guru Scan',
            'presensi_kode' => 'NUMA-GRU-TESTCODE001',
            'face_descriptor' => $descriptor,
        ]);

        $service = app(PresensiScanService::class);
        $result = $service->recordFace('guru', $descriptor);

        $this->assertTrue($result['ok']);
        $this->assertSame('Guru Scan', $result['nama']);
        $this->assertDatabaseHas('presensi_gurus', [
            'guru_id' => $guru->id,
            'status' => 'hadir',
            'metode' => 'face',
        ]);
    }

    public function test_barcode_api_rejects_unknown_code(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->postJson(route('presensi.scan.barcode', 'siswa'), ['kode' => 'NUMA-SIS-UNKNOWN'])
            ->assertStatus(422)
            ->assertJson(['ok' => false]);
    }

    public function test_face_descriptor_matcher_finds_closest(): void
    {
        $probe = array_fill(0, 128, 0.5);
        $match = FaceDescriptorMatcher::bestMatch($probe, [
            ['id' => 1, 'nama' => 'A', 'descriptor' => array_fill(0, 128, 0.9)],
            ['id' => 2, 'nama' => 'B', 'descriptor' => array_fill(0, 128, 0.51)],
        ]);

        $this->assertNotNull($match);
        $this->assertSame(2, $match['id']);
    }
}
