<?php

namespace Tests\Feature;

use App\Models\AkuntansiAkun;
use App\Models\AkuntansiJurnal;
use App\Models\AkuntansiJurnalLine;
use App\Models\User;
use App\Support\AkuntansiDefaults;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganCoaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_coa_after_defaults(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        AkuntansiDefaults::ensureForSekolah(1);

        $this->actingAs($admin)
            ->get(route('keuangan.coa.index'))
            ->assertOk()
            ->assertSee('101')
            ->assertSee('Daftar akun');
    }

    public function test_admin_can_create_custom_account(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('keuangan.coa.store'), [
                'kode' => '699',
                'nama' => 'Beban uji',
                'tipe' => 'beban',
                'is_active' => '1',
            ])
            ->assertRedirect(route('keuangan.coa.index'));

        $this->assertDatabaseHas('akuntansi_akuns', [
            'sekolah_id' => 1,
            'kode' => '699',
            'nama' => 'Beban uji',
        ]);
    }

    public function test_cannot_delete_reserved_system_account(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $kas = AkuntansiDefaults::ensureForSekolah(1)['kas'];

        $this->actingAs($admin)
            ->delete(route('keuangan.coa.destroy', $kas))
            ->assertRedirect(route('keuangan.coa.index'))
            ->assertSessionHasErrors('coa');

        $this->assertDatabaseHas('akuntansi_akuns', ['id' => $kas->id]);
    }

    public function test_cannot_delete_account_referenced_by_jurnal(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        $defaults = AkuntansiDefaults::ensureForSekolah(1);
        $kas = $defaults['kas'];

        $akun = AkuntansiAkun::query()->create([
            'sekolah_id' => 1,
            'kode' => '888',
            'nama' => 'Akun hapus tes',
            'tipe' => 'beban',
            'is_active' => true,
        ]);

        $jurnal = AkuntansiJurnal::query()->create([
            'sekolah_id' => 1,
            'tanggal' => '2026-05-01',
            'no_bukti' => null,
            'keterangan' => 'Tes',
            'sumber_type' => null,
            'sumber_id' => null,
            'dibuat_oleh' => $admin->id,
        ]);

        AkuntansiJurnalLine::query()->create([
            'sekolah_id' => 1,
            'jurnal_id' => $jurnal->id,
            'akun_id' => $akun->id,
            'debit' => 100,
            'kredit' => 0,
        ]);
        AkuntansiJurnalLine::query()->create([
            'sekolah_id' => 1,
            'jurnal_id' => $jurnal->id,
            'akun_id' => $kas->id,
            'debit' => 0,
            'kredit' => 100,
        ]);

        $this->actingAs($admin)
            ->delete(route('keuangan.coa.destroy', $akun))
            ->assertRedirect(route('keuangan.coa.index'))
            ->assertSessionHasErrors('coa');

        $this->assertDatabaseHas('akuntansi_akuns', ['id' => $akun->id]);
    }

    public function test_can_delete_unused_custom_account(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1, 'cabang_id' => 1]);
        $admin->assignRole('admin');

        AkuntansiDefaults::ensureForSekolah(1);

        $akun = AkuntansiAkun::query()->create([
            'sekolah_id' => 1,
            'kode' => '777',
            'nama' => 'Tanpa jurnal',
            'tipe' => 'beban',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('keuangan.coa.destroy', $akun))
            ->assertRedirect(route('keuangan.coa.index'));

        $this->assertDatabaseMissing('akuntansi_akuns', ['id' => $akun->id]);
    }
}
