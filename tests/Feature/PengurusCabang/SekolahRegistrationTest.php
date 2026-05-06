<?php

namespace Tests\Feature\PengurusCabang;

use App\Models\Cabang;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SekolahRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_register_school_and_operator(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC Test', 'kode' => 'TST']);
        $super = User::factory()->create();
        $super->assignRole('super_admin');

        $domain = config('tenancy.operator_email_domain', 'numa.com');

        $response = $this->actingAs($super)->post(route('pengurus.sekolah.store'), [
            'cabang_id' => $cabang->id,
            'npsn' => '87654321',
            'nama' => 'MI Contoh',
            'jenjang' => 'mi',
            'operator_name' => 'Budi Operator',
        ]);

        $response->assertRedirect(route('pengurus.sekolah.index'));
        $this->assertDatabaseHas('sekolahs', ['npsn' => '87654321', 'nama' => 'MI Contoh', 'jenjang' => 'mi']);

        $expectedEmail = '87654321@'.$domain;
        $this->assertDatabaseHas('users', ['email' => $expectedEmail]);

        $operator = User::query()->where('email', $expectedEmail)->firstOrFail();
        $this->assertTrue($operator->hasRole('admin'));
        $sekolahId = Sekolah::query()->where('npsn', '87654321')->value('id');
        $this->assertSame((int) $sekolahId, (int) $operator->sekolah_id);
    }
}
