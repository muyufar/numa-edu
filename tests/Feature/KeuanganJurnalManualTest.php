<?php

namespace Tests\Feature;

use App\Models\AkuntansiJurnal;
use App\Models\AkuntansiJurnalLine;
use App\Models\User;
use App\Support\AkuntansiDefaults;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganJurnalManualTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_balanced_manual_jurnal(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $a = AkuntansiDefaults::ensureForSekolah(1);
        $kas = $a['kas'];
        $pendapatan = $a['pendapatan'];

        $payload = [
            'tanggal' => '2026-05-10',
            'keterangan' => 'Penyesuaian tes',
            'lines' => [
                ['akun_id' => $kas->id, 'debit' => '50000', 'kredit' => ''],
                ['akun_id' => $pendapatan->id, 'debit' => '', 'kredit' => '50000'],
            ],
        ];

        $this->actingAs($admin)
            ->post(route('akuntansi.jurnal.store'), $payload)
            ->assertRedirect();

        $j = AkuntansiJurnal::query()->whereNull('sumber_type')->whereNull('sumber_id')->first();
        $this->assertNotNull($j);
        $this->assertSame(2, AkuntansiJurnalLine::query()->where('jurnal_id', $j->id)->count());
    }

    public function test_unbalanced_jurnal_is_rejected(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $a = AkuntansiDefaults::ensureForSekolah(1);

        $this->actingAs($admin)
            ->from(route('akuntansi.jurnal.create'))
            ->post(route('akuntansi.jurnal.store'), [
                'tanggal' => '2026-05-10',
                'lines' => [
                    ['akun_id' => $a['kas']->id, 'debit' => '100', 'kredit' => ''],
                    ['akun_id' => $a['pendapatan']->id, 'debit' => '', 'kredit' => '50'],
                ],
            ])
            ->assertRedirect(route('akuntansi.jurnal.create'))
            ->assertSessionHasErrors('lines');
    }

    public function test_can_delete_only_manual_jurnal(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $a = AkuntansiDefaults::ensureForSekolah(1);

        $manual = AkuntansiJurnal::query()->create([
            'sekolah_id' => 1,
            'tanggal' => '2026-05-10',
            'keterangan' => 'Manual',
            'sumber_type' => null,
            'sumber_id' => null,
            'dibuat_oleh' => $admin->id,
        ]);
        AkuntansiJurnalLine::query()->create([
            'sekolah_id' => 1,
            'jurnal_id' => $manual->id,
            'akun_id' => $a['kas']->id,
            'debit' => 10,
            'kredit' => 0,
        ]);
        AkuntansiJurnalLine::query()->create([
            'sekolah_id' => 1,
            'jurnal_id' => $manual->id,
            'akun_id' => $a['pendapatan']->id,
            'debit' => 0,
            'kredit' => 10,
        ]);

        $linked = AkuntansiJurnal::query()->create([
            'sekolah_id' => 1,
            'tanggal' => '2026-05-11',
            'keterangan' => 'Pembayaran',
            'sumber_type' => \App\Models\Pembayaran::class,
            'sumber_id' => 999,
            'dibuat_oleh' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('akuntansi.jurnal.destroy', $linked))
            ->assertRedirect(route('akuntansi.jurnal.index'))
            ->assertSessionHasErrors('jurnal');

        $this->assertDatabaseHas('akuntansi_jurnals', ['id' => $linked->id]);

        $this->actingAs($admin)
            ->delete(route('akuntansi.jurnal.destroy', $manual))
            ->assertRedirect(route('akuntansi.jurnal.index'));

        $this->assertDatabaseMissing('akuntansi_jurnals', ['id' => $manual->id]);
    }

    public function test_admin_can_view_jurnal_detail(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $a = AkuntansiDefaults::ensureForSekolah(1);

        $jurnal = AkuntansiJurnal::query()->create([
            'sekolah_id' => 1,
            'tanggal' => '2026-05-10',
            'keterangan' => 'Lihat detail',
            'sumber_type' => null,
            'sumber_id' => null,
            'dibuat_oleh' => $admin->id,
        ]);
        AkuntansiJurnalLine::query()->create([
            'sekolah_id' => 1,
            'jurnal_id' => $jurnal->id,
            'akun_id' => $a['kas']->id,
            'debit' => 1000,
            'kredit' => 0,
        ]);
        AkuntansiJurnalLine::query()->create([
            'sekolah_id' => 1,
            'jurnal_id' => $jurnal->id,
            'akun_id' => $a['pendapatan']->id,
            'debit' => 0,
            'kredit' => 1000,
        ]);

        $this->actingAs($admin)
            ->get(route('akuntansi.jurnal.show', $jurnal))
            ->assertOk()
            ->assertSee('Detail jurnal')
            ->assertSee('Lihat detail');
    }

    public function test_jurnal_export_csv_ok(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        AkuntansiDefaults::ensureForSekolah(1);

        $this->actingAs($admin)
            ->get(route('akuntansi.jurnal.export', ['tanggal_from' => '2026-05-01', 'tanggal_to' => '2026-05-31']))
            ->assertOk();
    }
}
