<?php

namespace Tests\Feature;

use App\Imports\SiswaImport;
use App\Models\Siswa;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_stores_nis_and_nisn_separately(): void
    {
        $this->seed(RoleSeeder::class);

        $import = new SiswaImport();
        $import->collection(collect([
            [
                'nis' => 'SCH-001',
                'nisn' => '0123456789',
                'nama_lengkap' => 'Siswa Import',
            ],
        ]));

        $siswa = Siswa::query()->where('nis', 'SCH-001')->first();

        $this->assertNotNull($siswa);
        $this->assertSame('SCH-001', $siswa->nis);
        $this->assertSame('0123456789', $siswa->nisn);
        $this->assertSame(1, $import->created);
        $this->assertSame('0123456789@numaedu.id', $siswa->fresh()->user?->email);
    }

    public function test_import_legacy_nisn_only_uses_it_as_nis(): void
    {
        $this->seed(RoleSeeder::class);

        $import = new SiswaImport();
        $import->collection(collect([
            [
                'nisn' => '9988776655',
                'nama_lengkap' => 'Siswa Legacy',
            ],
        ]));

        $siswa = Siswa::query()->where('nisn', '9988776655')->first();

        $this->assertNotNull($siswa);
        $this->assertSame('9988776655', $siswa->nis);
        $this->assertSame('9988776655', $siswa->nisn);
    }

    public function test_import_updates_existing_by_nisn(): void
    {
        $this->seed(RoleSeeder::class);

        Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'nis' => 'OLD-NIS',
            'nisn' => '0123456789',
            'nama' => 'Nama Lama',
        ]);

        $import = new SiswaImport();
        $import->collection(collect([
            [
                'nis' => 'NEW-NIS',
                'nisn' => '0123456789',
                'nama_lengkap' => 'Nama Baru',
            ],
        ]));

        $siswa = Siswa::query()->where('nisn', '0123456789')->first();

        $this->assertNotNull($siswa);
        $this->assertSame('NEW-NIS', $siswa->nis);
        $this->assertSame('Nama Baru', $siswa->nama);
        $this->assertSame(1, $import->updated);
    }
}
