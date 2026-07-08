<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use App\Support\SiswaAkunEmail;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiswaAkunTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_with_nisn_auto_generates_user_on_create(): void
    {
        $this->seed(RoleSeeder::class);

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'nis' => 'NIS-AUTO-1',
            'nisn' => '0123456789',
            'nama' => 'Siswa Auto',
        ]);

        $siswa->refresh();

        $this->assertNotNull($siswa->user_id);
        $this->assertSame('0123456789@numaedu.id', $siswa->user?->email);
        $this->assertTrue($siswa->user?->hasRole('siswa'));
        $this->assertTrue(Hash::check('0123456789', $siswa->user?->password ?? ''));
    }

    public function test_siswa_without_nisn_does_not_auto_generate_user(): void
    {
        $this->seed(RoleSeeder::class);

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'nis' => 'NIS-NO-NISN',
            'nama' => 'Tanpa NISN',
        ]);

        $this->assertNull($siswa->user_id);
    }

    public function test_admin_can_manually_trigger_buat_akun_from_nisn(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'nis' => 'NIS-MANUAL',
            'nisn' => '9988776655',
            'nama' => 'Manual Akun',
        ]);

        $orphanUser = $siswa->user;
        $siswa->forceFill(['user_id' => null])->saveQuietly();
        $orphanUser?->delete();

        $this->actingAs($admin)
            ->post(route('siswa.buat-akun', $siswa))
            ->assertRedirect(route('siswa.edit', $siswa));

        $this->assertSame('9988776655@numaedu.id', $siswa->fresh()->user?->email);
    }

    public function test_email_helper_normalizes_nisn(): void
    {
        $this->assertSame('0123456789@numaedu.id', SiswaAkunEmail::fromNisn(' 0123456789 '));
        $this->assertNull(SiswaAkunEmail::fromNisn(''));
    }
}
