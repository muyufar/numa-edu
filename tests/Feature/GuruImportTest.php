<?php

namespace Tests\Feature;

use App\Imports\GuruImport;
use App\Models\Guru;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_guru_from_gtk_row(): void
    {
        $this->seed(RoleSeeder::class);

        $import = new GuruImport();
        $import->collection(collect([
            [
                'nama_lengkap' => 'MAZIYATURROHMAH MAGHFUROH S.PD',
                'nip' => null,
                'nuptk' => '23587566',
                'jenis_kelamin' => 'Perempuan',
                'nomor_handphone' => "'6281392530690",
                'email' => 'maziyaturrohmah.maghfuroh@gmail.com',
                'email_akun_madrasah_digital' => 'maziyaturrohmahmaghfuroh@madrasah.kemenag.go.id',
                'password_awal' => 'MhMh2022#',
            ],
        ]));

        $guru = Guru::query()->where('nama', 'MAZIYATURROHMAH MAGHFUROH S.PD')->first();

        $this->assertNotNull($guru);
        $this->assertNull($guru->nip);
        $this->assertSame('P', $guru->jenis_kelamin);
        $this->assertSame('6281392530690', $guru->phone);
        $this->assertSame('maziyaturrohmahmaghfuroh@madrasah.kemenag.go.id', $guru->user?->email);
        $this->assertTrue($guru->user?->hasRole('guru'));
        $this->assertSame(1, $import->created);
        $this->assertSame(0, $import->skipped);
    }

    public function test_import_updates_existing_guru_by_nip(): void
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create([
            'email' => 'lama@example.com',
            'name' => 'Nama Lama',
        ]);
        $user->assignRole('guru');

        Guru::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'user_id' => $user->id,
            'nip' => '197905032006041028',
            'nama' => 'Nama Lama',
        ]);

        $import = new GuruImport();
        $import->collection(collect([
            [
                'nama_lengkap' => 'SUPRIYANTO S.SOS.I',
                'nip' => "'197905032006041028",
                'jenis_kelamin' => 'Laki-laki',
                'nomor_handphone' => "'6285743849205",
                'email' => 'ppprimak@gmail.comp',
                'email_akun_madrasah_digital' => 'supriyanto96@madrasah.kemenag.go.id',
                'password_awal' => 'SoSo2022#',
            ],
        ]));

        $guru = Guru::query()->where('nip', '197905032006041028')->first();

        $this->assertNotNull($guru);
        $this->assertSame('SUPRIYANTO S.SOS.I', $guru->nama);
        $this->assertSame('supriyanto96@madrasah.kemenag.go.id', $guru->user?->email);
        $this->assertSame(1, $import->updated);
    }

    public function test_import_skips_row_without_email(): void
    {
        $this->seed(RoleSeeder::class);

        $import = new GuruImport();
        $import->collection(collect([
            [
                'nama_lengkap' => 'Guru Tanpa Email',
                'jenis_kelamin' => 'Laki-laki',
            ],
        ]));

        $this->assertSame(0, Guru::query()->count());
        $this->assertSame(1, $import->skipped);
    }
}
