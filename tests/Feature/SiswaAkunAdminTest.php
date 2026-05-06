<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiswaAkunAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_student_account_email_and_reset_password(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $akunSiswa = User::factory()->create([
            'sekolah_id' => 1,
            'email' => 'siswaku@example.com',
            'password' => Hash::make('oldpassword'),
            'jenis_akun' => 'siswa',
        ]);
        $akunSiswa->assignRole('siswa');

        $siswa = Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'user_id' => $akunSiswa->id,
            'kelas_id' => null,
            'nis' => 'NIS-AKUN-'.uniqid(),
            'nama' => 'Siswa Akun',
            'tanggal_lahir' => '2011-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => null,
        ]);

        $this->actingAs($admin);

        $this->put(route('siswa.akun.update', $siswa), [
            'email' => 'siswaku2@example.com',
        ])->assertRedirect(route('siswa.edit', $siswa));

        $this->assertDatabaseHas('users', [
            'id' => $akunSiswa->id,
            'email' => 'siswaku2@example.com',
        ]);

        $this->post(route('siswa.akun.reset-password', $siswa), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('siswa.edit', $siswa));
    }
}

