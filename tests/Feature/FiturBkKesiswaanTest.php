<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\InventarisBarang;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FiturBkKesiswaanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function admin(): User
    {
        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '99998888',
            'nama' => 'SMP Test',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_bk_dashboard_accessible(): void
    {
        $this->actingAs($this->admin())
            ->get(route('bk.dashboard'))
            ->assertOk()
            ->assertSee(__('Dashboard BK'), false);
    }

    public function test_siswa_can_upload_profile_document(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $admin->sekolah_id,
            'nama' => 'Ani',
            'nis' => '2001',
        ]);

        $this->actingAs($admin)
            ->put(route('siswa.dokumen.update', $siswa), [
                'foto_siswa' => UploadedFile::fake()->image('foto.jpg'),
            ])
            ->assertRedirect(route('siswa.edit', $siswa));

        $siswa->refresh();
        $this->assertNotNull($siswa->foto_siswa_path);
        Storage::disk('public')->assertExists($siswa->foto_siswa_path);
    }

    public function test_inventaris_export_xls(): void
    {
        $admin = $this->admin();
        InventarisBarang::withoutGlobalScopes()->create([
            'sekolah_id' => $admin->sekolah_id,
            'nama' => 'Meja',
            'satuan' => 'unit',
            'stok_awal' => 5,
            'stok_minimum' => 1,
            'kondisi' => 'normal',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('inventaris.barang.index', ['export' => 1]))
            ->assertOk();
    }
}
