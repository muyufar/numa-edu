<?php

namespace Tests\Feature;

use App\Imports\GtkSheetReader;
use App\Imports\PegawaiImport;
use App\Models\Pegawai;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PegawaiImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_pegawai_from_gtk_row(): void
    {
        $this->seed(RoleSeeder::class);

        $import = new PegawaiImport();
        $import->collection(collect([
            [
                'nama_lengkap' => 'MUSLIKAH RUJI ETTY',
                'nip' => null,
                'tugas' => 'Tenaga Administrasi',
            ],
        ]));

        $pegawai = Pegawai::query()->where('nama', 'MUSLIKAH RUJI ETTY')->first();

        $this->assertNotNull($pegawai);
        $this->assertNull($pegawai->nip);
        $this->assertSame('Tenaga Administrasi', $pegawai->jabatan);
        $this->assertTrue($pegawai->is_active);
        $this->assertSame(1, $import->created);
    }

    public function test_import_updates_existing_pegawai_by_nip(): void
    {
        $this->seed(RoleSeeder::class);

        Pegawai::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'nama' => 'Nama Lama',
            'nip' => '3308094701830002',
            'jabatan' => 'TU',
            'is_active' => false,
        ]);

        $import = new PegawaiImport();
        $import->collection(collect([
            [
                'nama_lengkap' => 'BANATI ARIFAH',
                'nip' => "'3308094701830002",
                'tugas' => 'Satpam',
            ],
        ]));

        $pegawai = Pegawai::query()->where('nip', '3308094701830002')->first();

        $this->assertNotNull($pegawai);
        $this->assertSame('BANATI ARIFAH', $pegawai->nama);
        $this->assertSame('Satpam', $pegawai->jabatan);
        $this->assertTrue($pegawai->is_active);
        $this->assertSame(1, $import->updated);
    }

    public function test_gtk_sheet_reader_reads_tenaga_kependidikan_sheet(): void
    {
        $path = base_path('Daftar_GTK (1).xlsx');
        if (! is_file($path)) {
            $this->markTestSkipped('Fixture GTK tidak tersedia.');
        }

        $rows = GtkSheetReader::rows($path, PegawaiImport::SHEET_NAME, PegawaiImport::SHEET_INDEX);

        $this->assertGreaterThanOrEqual(1, $rows->count());
        $this->assertSame('MUSLIKAH RUJI ETTY', trim((string) $rows->first()['nama_lengkap']));
    }
}
