<?php

namespace Tests\Feature;

use App\Models\PpdbRegistration;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PpdbNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_ppdb_notifies_admins_not_whatsapp_api(): void
    {
        $this->seed(RoleSeeder::class);

        Notification::fake();

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $this->post(route('ppdb.daftar.store'), [
            'nama' => 'Calon Siswa WA',
            'no_hp_ortu' => '081234567890',
        ])->assertRedirect(route('ppdb.daftar'));

        Notification::assertSentTo($admin, \App\Notifications\PpdbRegistrationSubmitted::class);
    }

    public function test_ppdb_whatsapp_manual_link_is_generated(): void
    {
        $registration = PpdbRegistration::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'nama' => 'Calon Siswa',
            'no_hp_ortu' => '081234567890',
            'status' => 'verified',
        ]);

        $url = $registration->whatsappUrl();

        $this->assertNotNull($url);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
        $this->assertStringContainsString(rawurlencode('Calon Siswa'), $url);
        $this->assertStringContainsString('Diproses', $registration->whatsappMessage());
    }

    public function test_admin_status_change_does_not_call_whatsapp_api(): void
    {
        $this->seed(RoleSeeder::class);

        Notification::fake();

        $admin = User::factory()->create(['sekolah_id' => 1]);
        $admin->assignRole('admin');

        $registration = PpdbRegistration::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'nama' => 'Calon Update',
            'no_hp_ortu' => '628111222333',
            'status' => 'submitted',
        ]);

        $this->actingAs($admin)
            ->put(route('ppdb.update', $registration), [
                'nama' => 'Calon Update',
                'status' => 'verified',
            ])
            ->assertRedirect(route('ppdb.show', $registration))
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_phone_number_normalizes_indonesia_format(): void
    {
        $this->assertSame('6281234567890', \App\Support\WhatsApp\PhoneNumber::normalize('081234567890'));
        $this->assertSame('628111222333', \App\Support\WhatsApp\PhoneNumber::normalize('628111222333'));
    }
}
