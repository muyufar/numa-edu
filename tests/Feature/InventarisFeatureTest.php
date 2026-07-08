<?php

namespace Tests\Feature;

use App\Models\InventarisBarang;
use App\Models\InventarisKategori;
use App\Models\InventarisMutasi;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarisFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_barang_can_have_kondisi(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $kategori = InventarisKategori::query()->create(['nama' => 'ATK']);

        $this->actingAs($admin)
            ->post(route('inventaris.barang.store'), [
                'inventaris_kategori_id' => $kategori->id,
                'nama' => 'Meja Rusak',
                'satuan' => 'unit',
                'stok_awal' => 1,
                'stok_minimum' => 0,
                'kondisi' => 'rusak',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('inventaris_barangs', [
            'nama' => 'Meja Rusak',
            'kondisi' => 'rusak',
        ]);
    }

    public function test_mutasi_masuk_requires_sumber_pengadaan(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $barang = InventarisBarang::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'nama' => 'Laptop',
            'satuan' => 'unit',
            'stok_awal' => 0,
            'stok_minimum' => 0,
            'kondisi' => 'normal',
        ]);

        $this->actingAs($admin)
            ->post(route('inventaris.mutasi.store'), [
                'inventaris_barang_id' => $barang->id,
                'tanggal' => now()->toDateString(),
                'tipe' => 'in',
                'jumlah' => 2,
            ])
            ->assertSessionHasErrors('sumber_pengadaan');

        $this->actingAs($admin)
            ->post(route('inventaris.mutasi.store'), [
                'inventaris_barang_id' => $barang->id,
                'tanggal' => now()->toDateString(),
                'tipe' => 'in',
                'sumber_pengadaan' => 'bos',
                'jumlah' => 2,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('inventaris_mutasis', [
            'inventaris_barang_id' => $barang->id,
            'tipe' => 'in',
            'sumber_pengadaan' => 'bos',
            'jumlah' => 2,
        ]);
    }
}
