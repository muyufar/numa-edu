<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganRekapTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_rekap_page(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $kelas = Kelas::query()->create([
            'sekolah_id' => 1,
            'tingkat' => 1,
            'nama' => 'A',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $siswa = Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-501',
            'nama' => 'Rekap',
        ]);

        $tagihan = Tagihan::query()->create([
            'siswa_id' => $siswa->id,
            'jenis' => 'SPP',
            'periode' => '2026-04',
            'jumlah' => 10000,
            'status' => 'unpaid',
        ]);

        Pembayaran::query()->create([
            'tagihan_id' => $tagihan->id,
            'jumlah' => 4000,
            'metode' => 'tunai',
            'dibayar_pada' => now(),
            'dicatat_oleh' => $admin->id,
        ]);

        $tagihan->refreshStatus();

        $this->actingAs($admin)
            ->get(route('keuangan.rekap.index', ['periode_from' => '2026-04', 'periode_to' => '2026-04']))
            ->assertOk()
            ->assertSee('Rekap keuangan');

        $this->actingAs($admin)
            ->get(route('keuangan.rekap.siswa', ['siswa' => $siswa->id, 'periode_from' => '2026-04', 'periode_to' => '2026-04']))
            ->assertOk()
            ->assertSee('Detail piutang siswa');

        $this->actingAs($admin)
            ->get(route('keuangan.rekap.kelas', ['kelas' => $kelas->id, 'periode_from' => '2026-04', 'periode_to' => '2026-04']))
            ->assertOk()
            ->assertSee('Detail piutang kelas');
    }
}

