<?php

namespace Tests\Feature;

use App\Models\AkuntansiJurnalLine;
use App\Models\PengeluaranKas;
use App\Models\User;
use App\Support\AkuntansiDefaults;
use App\Support\PengeluaranKasService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganPengeluaranDanBukuKasTest extends TestCase
{
    use RefreshDatabase;

    public function test_buku_kas_ok_when_user_has_no_sekolah_id(): void
    {
        $this->seed(RoleSeeder::class);

        $super = User::factory()->create([
            'sekolah_id' => null,
            'cabang_id' => 1,
        ]);
        $super->assignRole('super_admin');

        AkuntansiDefaults::ensureForSekolah(1);

        $this->actingAs($super)
            ->get(route('keuangan.buku-kas.index', ['tanggal_from' => '2026-05-01', 'tanggal_to' => '2026-05-31']))
            ->assertOk()
            ->assertSee('Buku kas');
    }

    public function test_admin_can_create_pengeluaran_and_jurnal_balanced(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('keuangan.pengeluaran-kas.store'), [
                'tanggal' => '2026-05-02',
                'jumlah' => 150000,
                'keterangan' => 'ATK',
                'no_bukti' => 'BK-01',
                'akun_beban_id' => '',
            ])
            ->assertRedirect(route('keuangan.pengeluaran-kas.index'));

        $this->assertDatabaseHas('pengeluaran_kass', [
            'sekolah_id' => 1,
            'keterangan' => 'ATK',
            'jumlah' => 150000,
        ]);

        $p = PengeluaranKas::query()->first();
        $this->assertNotNull($p?->akuntansi_jurnal_id);

        $lines = AkuntansiJurnalLine::query()->where('jurnal_id', $p->akuntansi_jurnal_id)->get();
        $this->assertCount(2, $lines);
        $this->assertEqualsWithDelta(150000.0, (float) $lines->sum('debit'), 0.01);
        $this->assertEqualsWithDelta(150000.0, (float) $lines->sum('kredit'), 0.01);
    }

    public function test_buku_kas_page_loads_after_pengeluaran(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        PengeluaranKasService::create(1, $admin->id, [
            'tanggal' => '2026-05-02',
            'jumlah' => 50000,
            'keterangan' => 'Listrik',
        ]);

        $this->actingAs($admin)
            ->get(route('keuangan.buku-kas.index', ['tanggal_from' => '2026-05-01', 'tanggal_to' => '2026-05-31']))
            ->assertOk()
            ->assertSee('Buku kas');

        $this->actingAs($admin)
            ->get(route('keuangan.buku-kas.export', ['tanggal_from' => '2026-05-01', 'tanggal_to' => '2026-05-31']))
            ->assertOk();
    }

    public function test_destroy_pengeluaran_removes_jurnal(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $p = PengeluaranKasService::create(1, $admin->id, [
            'tanggal' => '2026-05-02',
            'jumlah' => 10000,
            'keterangan' => 'Hapus uji',
        ]);
        $jid = $p->akuntansi_jurnal_id;
        $this->assertNotNull($jid);

        $this->actingAs($admin)
            ->delete(route('keuangan.pengeluaran-kas.destroy', $p))
            ->assertRedirect(route('keuangan.pengeluaran-kas.index'));

        $this->assertDatabaseMissing('pengeluaran_kass', ['id' => $p->id]);
        $this->assertDatabaseMissing('akuntansi_jurnals', ['id' => $jid]);
    }
}
