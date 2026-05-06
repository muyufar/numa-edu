<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WilayahProxyTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengurus_cabang_can_hit_wilayah_proxy_without_school_context(): void
    {
        Role::firstOrCreate(['name' => 'pengurus_cabang', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);
        $user = User::factory()->create(['cabang_id' => $cabang->id]);
        $user->assignRole('pengurus_cabang');

        Http::fake([
            'https://wilayah.id/api/*' => Http::response(['data' => [['code' => '31', 'name' => 'DKI Jakarta']], 'meta' => []], 200),
        ]);

        $this->actingAs($user)
            ->get(route('ref.wilayah.provinces'))
            ->assertOk()
            ->assertJsonPath('data.0.code', '31');
    }
}
