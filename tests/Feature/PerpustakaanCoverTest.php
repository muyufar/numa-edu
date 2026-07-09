<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\PerpustakaanBuku;
use App\Models\Sekolah;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PerpustakaanCoverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_cover_route_serves_uploaded_image(): void
    {
        Storage::fake('public');

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $sekolah = Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '88889999',
            'nama' => 'SMP Perpus',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin_perpus');

        $cover = UploadedFile::fake()->image('cover.jpg', 200, 300);

        $this->actingAs($admin)
            ->post(route('perpustakaan.buku.store'), [
                'judul' => 'Buku Cover',
                'tipe' => 'fisik',
                'jumlah_eksemplar' => 1,
                'is_active' => true,
                'cover' => $cover,
            ])
            ->assertRedirect();

        $buku = PerpustakaanBuku::query()->first();
        $this->assertNotNull($buku->cover_path);
        Storage::disk('public')->assertExists($buku->cover_path);
        $this->assertTrue($buku->hasCover());
        $this->assertSame('/storage/'.$buku->cover_path, $buku->coverUrl());

        $response = $this->actingAs($admin)->get(route('perpustakaan.buku.cover', $buku));

        $response->assertOk();
        $this->assertStringStartsWith('image/', (string) $response->headers->get('content-type'));
    }
}
