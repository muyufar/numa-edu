<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AkunOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function seedSiswaRow(): Siswa
    {
        $kelas = Kelas::query()->create([
            'tingkat' => 1,
            'nama' => 'OnboardingKelas',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        return Siswa::withoutGlobalScopes()->create([
            'user_id' => null,
            'kelas_id' => $kelas->id,
            'sekolah_id' => 1,
            'nis' => 'NIS-ONBOARD-'.uniqid(),
            'nama' => 'Anak Onboarding',
            'tanggal_lahir' => '2010-05-15',
            'jenis_kelamin' => 'L',
        ]);
    }

    public function test_wali_can_complete_onboarding_and_reach_dashboard(): void
    {
        $this->seed(RoleSeeder::class);
        $siswa = $this->seedSiswaRow();
        $npsn = Sekolah::query()->whereKey(1)->value('npsn');

        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('wali');

        $this->actingAs($user);

        $this->get(route('dashboard'))->assertRedirect(route('onboarding.hubungkan'));

        $this->post(route('onboarding.hubungkan.store'), [
            'npsn' => $npsn,
            'nis' => $siswa->nis,
            'tanggal_lahir' => '2010-05-15',
            'nama_siswa' => 'Anak Onboarding',
            'hubungan' => 'ibu',
        ])->assertRedirect(route('dashboard'));

        $this->assertTrue($user->fresh()->waliSiswas()->where('siswas.id', $siswa->id)->exists());
        $this->assertSame(1, (int) $user->fresh()->sekolah_id);
    }

    public function test_siswa_can_claim_profile_via_onboarding_with_nisn(): void
    {
        $this->seed(RoleSeeder::class);
        $siswa = $this->seedSiswaRow();
        $siswa->forceFill(['nisn' => '0123456789'])->saveQuietly();
        $npsn = Sekolah::query()->whereKey(1)->value('npsn');

        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('siswa');

        $this->actingAs($user);

        $this->post(route('onboarding.hubungkan.store'), [
            'npsn' => $npsn,
            'nis' => '0123456789',
            'tanggal_lahir' => '2010-05-15',
            'nama_siswa' => 'Anak Onboarding',
        ])->assertRedirect(route('dashboard'));

        $this->assertSame((int) $user->id, (int) $siswa->fresh()->user_id);
    }

    public function test_siswa_can_claim_profile_via_onboarding(): void
    {
        $this->seed(RoleSeeder::class);
        $siswa = $this->seedSiswaRow();
        $npsn = Sekolah::query()->whereKey(1)->value('npsn');

        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('siswa');

        $this->actingAs($user);

        $this->post(route('onboarding.hubungkan.store'), [
            'npsn' => $npsn,
            'nis' => $siswa->nis,
            'tanggal_lahir' => '2010-05-15',
            'nama_siswa' => 'Anak Onboarding',
        ])->assertRedirect(route('dashboard'));

        $this->assertSame((int) $user->id, (int) $siswa->fresh()->user_id);
        $this->assertSame(1, (int) $user->fresh()->sekolah_id);
    }

    public function test_login_is_not_forced_to_onboarding_when_not_authenticated(): void
    {
        $this->get(route('login'))->assertOk();
    }
}
