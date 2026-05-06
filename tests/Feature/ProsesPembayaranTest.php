<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KewajibanPembayaran;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProsesPembayaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_generate_and_pay_monthly_obligations(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'sekolah_id' => 1,
            'cabang_id' => 1,
        ]);
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
            'nis' => 'NIS-001',
            'nama' => 'Budi',
        ]);

        KewajibanPembayaran::query()->create([
            'sekolah_id' => 1,
            'nama' => 'SPP',
            'tipe' => 'bulanan',
            'nominal_default' => 10000,
            'batas_hari_bayar' => 15,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('keuangan.proses.generate'), [
                'siswa_id' => $siswa->id,
                'bulan' => 4,
                'tahun' => 2026,
            ])
            ->assertRedirect(route('keuangan.proses.index', [
                'siswa_id' => $siswa->id,
                'bulan' => 4,
                'tahun' => 2026,
            ]));

        $tagihan = Tagihan::query()->where('siswa_id', $siswa->id)->first();
        $this->assertNotNull($tagihan);
        $this->assertSame('SPP', $tagihan->jenis);
        $this->assertSame('2026-04', $tagihan->periode);

        $this->actingAs($admin)
            ->post(route('keuangan.proses.bayar'), [
                'siswa_id' => $siswa->id,
                'bulan' => 4,
                'tahun' => 2026,
                'tagihan_ids' => [$tagihan->id],
                'amounts' => [
                    (string) $tagihan->id => 5000,
                ],
                'metode' => Pembayaran::METODE_OPTIONS[0],
                'referensi' => 'TRX-001',
            ])
            ->assertRedirect();

        $tagihan->refresh();
        $this->assertSame('partial', $tagihan->status);

        $this->actingAs($admin)
            ->post(route('keuangan.proses.bayar'), [
                'siswa_id' => $siswa->id,
                'bulan' => 4,
                'tahun' => 2026,
                'tagihan_ids' => [$tagihan->id],
                'amounts' => [
                    (string) $tagihan->id => 5000,
                ],
                'metode' => Pembayaran::METODE_OPTIONS[0],
                'referensi' => 'TRX-002',
            ])
            ->assertRedirect();

        $tagihan->refresh();
        $this->assertSame('paid', $tagihan->status);

        $pembayaran = Pembayaran::query()->first();
        $this->assertNotNull($pembayaran);
        $this->assertNotNull($pembayaran->akuntansi_jurnal_id);
    }

    public function test_admin_can_generate_mass_for_class_and_not_duplicate(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'sekolah_id' => 1,
            'cabang_id' => 1,
        ]);
        $admin->assignRole('admin');

        $kelas = Kelas::query()->create([
            'sekolah_id' => 1,
            'tingkat' => 1,
            'nama' => 'A',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $s1 = Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-101',
            'nama' => 'Satu',
        ]);
        $s2 = Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-102',
            'nama' => 'Dua',
        ]);

        KewajibanPembayaran::query()->create([
            'sekolah_id' => 1,
            'nama' => 'SPP',
            'tipe' => 'bulanan',
            'nominal_default' => 10000,
            'batas_hari_bayar' => 15,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('keuangan.proses.generate-mass'), [
                'kelas_id' => $kelas->id,
                'bulan' => 4,
                'tahun' => 2026,
            ])
            ->assertRedirect(route('keuangan.proses.index', ['bulan' => 4, 'tahun' => 2026]));

        $this->assertSame(2, Tagihan::query()->count());
        $this->assertSame(1, Tagihan::query()->where('siswa_id', $s1->id)->count());
        $this->assertSame(1, Tagihan::query()->where('siswa_id', $s2->id)->count());

        // run again should not duplicate
        $this->actingAs($admin)
            ->post(route('keuangan.proses.generate-mass'), [
                'kelas_id' => $kelas->id,
                'bulan' => 4,
                'tahun' => 2026,
            ])
            ->assertRedirect();

        $this->assertSame(2, Tagihan::query()->count());
    }

    public function test_admin_can_generate_insidental_mass_and_not_duplicate(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'sekolah_id' => 1,
            'cabang_id' => 1,
        ]);
        $admin->assignRole('admin');

        $kelas = Kelas::query()->create([
            'sekolah_id' => 1,
            'tingkat' => 2,
            'nama' => 'B',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $s1 = Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-201',
            'nama' => 'Tiga',
        ]);
        $s2 = Siswa::query()->create([
            'sekolah_id' => 1,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-202',
            'nama' => 'Empat',
        ]);

        $ins = KewajibanPembayaran::query()->create([
            'sekolah_id' => 1,
            'nama' => 'Seragam',
            'tipe' => 'insidental',
            'nominal_default' => 75000,
            'batas_hari_bayar' => null,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('keuangan.proses.generate-insidental'), [
                'kewajiban_id' => $ins->id,
                'kelas_id' => $kelas->id,
                'bulan' => 5,
                'tahun' => 2026,
            ])
            ->assertRedirect(route('keuangan.proses.index', ['bulan' => 5, 'tahun' => 2026]));

        $this->assertSame(2, Tagihan::query()->count());
        $this->assertSame(1, Tagihan::query()->where('siswa_id', $s1->id)->where('jenis', 'Seragam')->count());
        $this->assertSame(1, Tagihan::query()->where('siswa_id', $s2->id)->where('jenis', 'Seragam')->count());

        // run again should not duplicate
        $this->actingAs($admin)
            ->post(route('keuangan.proses.generate-insidental'), [
                'kewajiban_id' => $ins->id,
                'kelas_id' => $kelas->id,
                'bulan' => 5,
                'tahun' => 2026,
            ])
            ->assertRedirect();

        $this->assertSame(2, Tagihan::query()->count());
    }
}

