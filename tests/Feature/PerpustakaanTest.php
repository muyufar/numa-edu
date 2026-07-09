<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\PerpustakaanBuku;
use App\Models\PerpustakaanPeminjaman;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PerpustakaanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function seedSekolah(): Sekolah
    {
        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);

        return Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '88889999',
            'nama' => 'SMP Perpus',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);
    }

    public function test_admin_perpus_can_create_digital_book(): void
    {
        Storage::fake('public');

        $sekolah = $this->seedSekolah();
        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin_perpus');

        $this->actingAs($admin)
            ->post(route('perpustakaan.buku.store'), [
                'judul' => 'Ensiklopedia IPA',
                'pengarang' => 'Tim Redaksi',
                'tipe' => 'digital',
                'is_active' => true,
                'file' => UploadedFile::fake()->create('buku.pdf', 120, 'application/pdf'),
            ])
            ->assertRedirect();

        $buku = PerpustakaanBuku::query()->first();
        $this->assertSame('digital', $buku->tipe);
        $this->assertNotNull($buku->file_path);
    }

    public function test_siswa_can_borrow_and_preview_digital_book(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('perpustakaan/ebooks/test.pdf', '%PDF-1.4 fake');

        $sekolah = $this->seedSekolah();
        $siswaUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $siswaUser->assignRole('siswa');

        Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $siswaUser->id,
            'nama' => 'Budi Siswa',
            'nis' => '1001',
        ]);

        $buku = PerpustakaanBuku::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'judul' => 'Buku Digital',
            'tipe' => 'digital',
            'jumlah_eksemplar' => 0,
            'eksemplar_tersedia' => 0,
            'file_path' => 'perpustakaan/ebooks/test.pdf',
            'file_name' => 'test.pdf',
            'mime' => 'application/pdf',
            'is_active' => true,
        ]);

        $this->actingAs($siswaUser)
            ->post(route('perpustakaan.buku.pinjam', $buku), ['tipe_peminjaman' => 'digital'])
            ->assertRedirect();

        $this->assertDatabaseHas('perpustakaan_peminjamans', [
            'user_id' => $siswaUser->id,
            'perpustakaan_buku_id' => $buku->id,
            'tipe_peminjaman' => 'digital',
            'status' => 'dipinjam',
        ]);

        $this->actingAs($siswaUser)
            ->get(route('perpustakaan.buku.preview', $buku))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_siswa_cannot_preview_without_loan(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('perpustakaan/ebooks/test.pdf', '%PDF-1.4 fake');

        $sekolah = $this->seedSekolah();
        $siswaUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $siswaUser->assignRole('siswa');

        Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $siswaUser->id,
            'nama' => 'Ani Siswa',
            'nis' => '1002',
        ]);

        $buku = PerpustakaanBuku::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'judul' => 'Buku Digital',
            'tipe' => 'digital',
            'file_path' => 'perpustakaan/ebooks/test.pdf',
            'file_name' => 'test.pdf',
            'mime' => 'application/pdf',
            'is_active' => true,
        ]);

        $this->actingAs($siswaUser)
            ->get(route('perpustakaan.buku.preview', $buku))
            ->assertForbidden();
    }

    public function test_admin_perpus_can_return_physical_book(): void
    {
        $sekolah = $this->seedSekolah();
        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        $guruUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $guruUser->assignRole('guru');
        $guru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $guruUser->id,
            'nama' => 'Pak Guru',
        ]);

        $buku = PerpustakaanBuku::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'judul' => 'Buku Fisik',
            'tipe' => 'fisik',
            'jumlah_eksemplar' => 2,
            'eksemplar_tersedia' => 1,
            'is_active' => true,
        ]);

        $peminjaman = PerpustakaanPeminjaman::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'perpustakaan_buku_id' => $buku->id,
            'user_id' => $guruUser->id,
            'guru_id' => $guru->id,
            'tipe_peminjaman' => 'fisik',
            'status' => 'dipinjam',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
        ]);

        $this->actingAs($admin)
            ->post(route('perpustakaan.peminjaman.kembalikan', $peminjaman))
            ->assertRedirect();

        $peminjaman->refresh();
        $buku->refresh();

        $this->assertSame('dikembalikan', $peminjaman->status);
        $this->assertSame(2, $buku->eksemplar_tersedia);
    }

    public function test_admin_can_access_pengaturan_settings(): void
    {
        $sekolah = $this->seedSekolah();
        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('perpustakaan.pengaturan.edit'))
            ->assertOk()
            ->assertSee(__('Maks. peminjaman aktif'), false);
    }

    public function test_siswa_catalog_defaults_to_digital_ebooks_only(): void
    {
        $sekolah = $this->seedSekolah();
        $siswaUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $siswaUser->assignRole('siswa');

        Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $siswaUser->id,
            'nama' => 'Citra Siswa',
            'nis' => '1003',
        ]);

        PerpustakaanBuku::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'judul' => 'E-Book IPA',
            'tipe' => 'digital',
            'file_path' => 'perpustakaan/ebooks/ipa.pdf',
            'is_active' => true,
        ]);

        PerpustakaanBuku::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'judul' => 'Buku Fisik Matematika',
            'tipe' => 'fisik',
            'jumlah_eksemplar' => 3,
            'eksemplar_tersedia' => 3,
            'is_active' => true,
        ]);

        PerpustakaanBuku::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'judul' => 'E-Book Nonaktif',
            'tipe' => 'digital',
            'file_path' => 'perpustakaan/ebooks/nonaktif.pdf',
            'is_active' => false,
        ]);

        $this->actingAs($siswaUser)
            ->get(route('perpustakaan.buku.index'))
            ->assertOk()
            ->assertSee(__('Perpustakaan digital'), false)
            ->assertSee('E-Book IPA', false)
            ->assertDontSee('Buku Fisik Matematika', false)
            ->assertDontSee('E-Book Nonaktif', false);
    }
}
