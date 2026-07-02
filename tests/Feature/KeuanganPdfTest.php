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

class KeuanganPdfTest extends TestCase
{
    use RefreshDatabase;

    private function seedSiswa(string $nis = 'NIS-PDF-001'): Siswa
    {
        $kelas = Kelas::query()->create([
            'sekolah_id' => 1,
            'tingkat' => 1,
            'nama' => 'A',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        return Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => $nis,
            'nama' => 'Ani PDF',
        ]);
    }

    public function test_admin_can_download_invoice_and_kwitansi_pdf(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $siswa = $this->seedSiswa();

        $tagihan = Tagihan::query()->create([
            'sekolah_id' => 1,
            'siswa_id' => $siswa->id,
            'jenis' => 'SPP',
            'periode' => '2026-04',
            'jumlah' => 150000,
            'status' => 'unpaid',
        ]);

        $pembayaran = PembayaranService::record($tagihan, [
            'jumlah' => 75000,
            'metode' => 'tunai',
            'referensi' => 'TRX-PDF-1',
        ], $admin->id);

        $this->actingAs($admin)
            ->get(route('tagihan.invoice.pdf', $tagihan))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($admin)
            ->get(route('pembayaran.kwitansi.pdf', $pembayaran))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_wali_linked_can_download_pdf_for_own_child(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $wali = User::factory()->create(['sekolah_id' => 1]);
        $wali->assignRole('wali');

        $siswa = $this->seedSiswa();
        $wali->waliSiswas()->attach($siswa->id, ['hubungan' => 'ibu']);

        $tagihan = Tagihan::query()->create([
            'sekolah_id' => 1,
            'siswa_id' => $siswa->id,
            'jenis' => 'SPP',
            'periode' => '2026-05',
            'jumlah' => 100000,
            'status' => 'unpaid',
        ]);

        $pembayaran = PembayaranService::record($tagihan, [
            'jumlah' => 50000,
            'metode' => 'transfer',
            'referensi' => 'TRX-WALI-1',
        ], $admin->id);

        $this->actingAs($wali)
            ->get(route('tagihan.invoice.pdf', $tagihan))
            ->assertOk();

        $this->actingAs($wali)
            ->get(route('pembayaran.kwitansi.pdf', $pembayaran))
            ->assertOk();
    }

    public function test_wali_cannot_download_pdf_for_unlinked_student(): void
    {
        $this->seed(RoleSeeder::class);

        $siswaLinked = $this->seedSiswa('NIS-PDF-LINK');
        $siswaOther = $this->seedSiswa('NIS-PDF-OTHER');

        $wali = User::factory()->create(['sekolah_id' => 1]);
        $wali->assignRole('wali');
        $wali->waliSiswas()->attach($siswaLinked->id, ['hubungan' => 'ibu']);

        $tagihan = Tagihan::query()->create([
            'sekolah_id' => 1,
            'siswa_id' => $siswaOther->id,
            'jenis' => 'SPP',
            'periode' => '2026-06',
            'jumlah' => 100000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($wali)
            ->get(route('tagihan.invoice.pdf', $tagihan))
            ->assertForbidden();
    }
}
