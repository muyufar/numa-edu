<?php

namespace Tests\Feature;

use App\Models\AkuntansiJurnalLine;
use App\Models\PemasukanKas;
use App\Models\PengeluaranKas;
use App\Models\User;
use App\Support\PemasukanKasService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KeuanganPemasukanDanBuktiNotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_pemasukan_kas_with_jurnal(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('keuangan.pemasukan-kas.store'), [
                'tanggal' => '2026-07-02',
                'jumlah' => 2500000,
                'keterangan' => 'Bantuan PC NU',
                'no_bukti' => 'BM-01',
                'akun_pendapatan_id' => '',
            ])
            ->assertRedirect(route('keuangan.pemasukan-kas.index'));

        $this->assertDatabaseHas('pemasukan_kass', [
            'sekolah_id' => 1,
            'keterangan' => 'Bantuan PC NU',
            'jumlah' => 2500000,
        ]);

        $p = PemasukanKas::query()->first();
        $this->assertNotNull($p?->akuntansi_jurnal_id);

        $lines = AkuntansiJurnalLine::query()->where('jurnal_id', $p->akuntansi_jurnal_id)->get();
        $this->assertCount(2, $lines);
        $this->assertEqualsWithDelta(2500000.0, (float) $lines->sum('debit'), 0.01);
        $this->assertEqualsWithDelta(2500000.0, (float) $lines->sum('kredit'), 0.01);
    }

    public function test_admin_can_upload_bukti_nota_for_pemasukan_and_pengeluaran(): void
    {
        Storage::fake('public');
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $file = UploadedFile::fake()->create('nota.pdf', 120, 'application/pdf');

        $this->actingAs($admin)
            ->post(route('keuangan.pemasukan-kas.store'), [
                'tanggal' => '2026-07-02',
                'jumlah' => 100000,
                'keterangan' => 'Dengan bukti',
                'bukti_nota' => $file,
            ])
            ->assertRedirect(route('keuangan.pemasukan-kas.index'));

        $pemasukan = PemasukanKas::query()->first();
        $this->assertNotNull($pemasukan?->bukti_nota_path);
        Storage::disk('public')->assertExists($pemasukan->bukti_nota_path);

        $this->actingAs($admin)
            ->get(route('keuangan.pemasukan-kas.bukti-nota', $pemasukan))
            ->assertOk();

        $file2 = UploadedFile::fake()->image('struk.jpg');

        $this->actingAs($admin)
            ->post(route('keuangan.pengeluaran-kas.store'), [
                'tanggal' => '2026-07-03',
                'jumlah' => 50000,
                'keterangan' => 'ATK dengan bukti',
                'bukti_nota' => $file2,
            ])
            ->assertRedirect(route('keuangan.pengeluaran-kas.index'));

        $pengeluaran = PengeluaranKas::query()->first();
        $this->assertNotNull($pengeluaran?->bukti_nota_path);

        $this->actingAs($admin)
            ->get(route('keuangan.pengeluaran-kas.bukti-nota', $pengeluaran))
            ->assertOk();
    }

    public function test_destroy_pemasukan_deletes_bukti_file(): void
    {
        Storage::fake('public');
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $path = 'keuangan/pemasukan/sekolah-1/test.pdf';
        Storage::disk('public')->put($path, 'pdf-content');

        $p = PemasukanKasService::create(1, $admin->id, [
            'tanggal' => '2026-07-02',
            'jumlah' => 10000,
            'keterangan' => 'Hapus bukti',
            'bukti_nota_path' => $path,
        ]);

        $this->actingAs($admin)
            ->delete(route('keuangan.pemasukan-kas.destroy', $p))
            ->assertRedirect(route('keuangan.pemasukan-kas.index'));

        Storage::disk('public')->assertMissing($path);
    }
}
