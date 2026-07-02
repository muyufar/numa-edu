<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use App\Support\PembayaranService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliKeuanganTest extends TestCase
{
    use RefreshDatabase;

    private function seedSiswa(string $nis = 'NIS-WALI-001'): Siswa
    {
        $kelas = Kelas::query()->create([
            'sekolah_id' => 1,
            'tingkat' => 2,
            'nama' => 'B',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        return Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => $nis,
            'nama' => 'Budi Wali',
        ]);
    }

    private function linkedWali(Siswa $siswa): User
    {
        $wali = User::factory()->create(['sekolah_id' => 1]);
        $wali->assignRole('wali');
        $wali->waliSiswas()->attach($siswa->id, ['hubungan' => 'ayah']);

        return $wali;
    }

    public function test_wali_can_view_tagihan_list_and_detail_with_payment_history(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $siswa = $this->seedSiswa();
        $wali = $this->linkedWali($siswa);

        $tagihan = Tagihan::query()->create([
            'sekolah_id' => 1,
            'siswa_id' => $siswa->id,
            'jenis' => 'SPP',
            'periode' => '2026-04',
            'jumlah' => 200000,
            'status' => 'unpaid',
        ]);

        PembayaranService::record($tagihan, [
            'jumlah' => 100000,
            'metode' => 'tunai',
            'referensi' => 'KW-001',
        ], $admin->id);

        $this->actingAs($wali)
            ->get(route('wali.tagihan.index', $siswa))
            ->assertOk()
            ->assertSee('SPP')
            ->assertSee('2026-04')
            ->assertSee(__('Riwayat pembayaran'));

        $this->actingAs($wali)
            ->get(route('wali.tagihan.show', [$siswa, $tagihan]))
            ->assertOk()
            ->assertSee(__('Detail tagihan'))
            ->assertSee('KW-001')
            ->assertSee(__('Unduh invoice PDF'));
    }

    public function test_wali_cannot_view_tagihan_for_unlinked_student(): void
    {
        $this->seed(RoleSeeder::class);

        $siswa = $this->seedSiswa();
        $other = $this->seedSiswa('NIS-WALI-002');

        $wali = User::factory()->create(['sekolah_id' => 1]);
        $wali->assignRole('wali');
        $wali->waliSiswas()->attach($siswa->id, ['hubungan' => 'ibu']);

        $this->actingAs($wali)
            ->get(route('wali.tagihan.index', $other))
            ->assertForbidden();
    }

    public function test_wali_cannot_view_tagihan_detail_if_not_owned_by_siswa(): void
    {
        $this->seed(RoleSeeder::class);

        $siswa = $this->seedSiswa();
        $other = $this->seedSiswa('NIS-WALI-003');
        $wali = $this->linkedWali($siswa);

        $tagihanOther = Tagihan::query()->create([
            'sekolah_id' => 1,
            'siswa_id' => $other->id,
            'jenis' => 'Uang Gedung',
            'periode' => '2026-04',
            'jumlah' => 500000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($wali)
            ->get(route('wali.tagihan.show', [$siswa, $tagihanOther]))
            ->assertNotFound();
    }
}
