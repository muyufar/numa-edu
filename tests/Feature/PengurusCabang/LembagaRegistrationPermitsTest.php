<?php

namespace Tests\Feature\PengurusCabang;

use App\Models\Cabang;
use App\Models\LembagaRegistration;
use App\Models\LembagaRegistrationPermit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LembagaRegistrationPermitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengurus_cabang_can_update_permit_metadata(): void
    {
        Storage::fake('public');
        Role::firstOrCreate(['name' => 'pengurus_cabang', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create(['nama' => 'PC Test', 'kode' => 'TST']);
        $pengurus = User::factory()->create(['cabang_id' => $cabang->id]);
        $pengurus->assignRole('pengurus_cabang');

        $reg = LembagaRegistration::query()->create([
            'public_token' => '33333333-3333-3333-3333-333333333333',
            'status' => LembagaRegistration::STATUS_AWAITING_MOU,
            'cabang_id' => $cabang->id,
            'npsn' => '20363711',
            'nama_lembaga' => 'MTs Test',
            'jenjang' => 'mts',
            'waktu_belajar' => 'pagi',
            'status_kkm' => 'ya',
            'komite' => 'ada',
            'operator_name' => 'Op',
            'operator_email' => 'op@test.test',
        ]);

        $perm = LembagaRegistrationPermit::query()->create([
            'lembaga_registration_id' => $reg->id,
            'sort_order' => 0,
            'permit_key' => 'sk_izin_operasional',
            'nama_sk' => 'SK IZIN OPERASIONAL',
            'nomor_sk' => null,
            'tanggal_sk' => null,
            'dokumen_path' => null,
        ]);

        $pdf = UploadedFile::fake()->create('izin.pdf', 100, 'application/pdf');

        $response = $this->actingAs($pengurus)->put(route('pengurus.lembaga-registrations.permits.update', $reg), [
            'permits' => [
                'sk_izin_operasional' => [
                    'nomor_sk' => 'SK-123/2020',
                    'tanggal_sk' => '2020-01-15',
                    'dokumen' => $pdf,
                ],
            ],
        ]);

        $response->assertRedirect(route('pengurus.lembaga-registrations.show', $reg));
        $response->assertSessionHas('status');

        $perm->refresh();
        $this->assertSame('SK-123/2020', $perm->nomor_sk);
        $this->assertSame('2020-01-15', $perm->tanggal_sk->format('Y-m-d'));
        $this->assertNotNull($perm->dokumen_path);
        Storage::disk('public')->assertExists($perm->dokumen_path);
    }
}
