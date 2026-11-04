<?php

namespace Tests\Unit;

use App\Models\LembagaRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LembagaRegistrationPublicLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_awaiting_mou_includes_mou_and_status_links(): void
    {
        $reg = LembagaRegistration::query()->create([
            'public_token' => '11111111-1111-1111-1111-111111111111',
            'status' => LembagaRegistration::STATUS_AWAITING_MOU,
            'cabang_id' => 1,
            'npsn' => '12345678',
            'nama_lembaga' => 'MI Test',
            'jenjang' => 'mi',
            'waktu_belajar' => 'pagi',
            'status_kkm' => 'ya',
            'komite' => 'ada',
            'operator_name' => 'Op',
            'operator_email' => 'op@test.test',
        ]);

        $keys = array_column($reg->publicPortalLinks(), 'key');

        $this->assertSame(['mou', 'status'], $keys);
        $this->assertStringContainsString($reg->public_token, $reg->publicMouUrl());
        $this->assertStringContainsString($reg->public_token, $reg->publicStatusUrl());
        $this->assertStringContainsString($reg->publicMouUrl(), $reg->publicPortalShareText());
    }

    public function test_rejected_includes_edit_link(): void
    {
        $reg = LembagaRegistration::query()->create([
            'public_token' => '22222222-2222-2222-2222-222222222222',
            'status' => LembagaRegistration::STATUS_REJECTED,
            'cabang_id' => 1,
            'npsn' => '87654321',
            'nama_lembaga' => 'MTs Test',
            'jenjang' => 'mts',
            'waktu_belajar' => 'pagi',
            'status_kkm' => 'ya',
            'komite' => 'ada',
            'operator_name' => 'Op',
            'operator_email' => 'op2@test.test',
        ]);

        $keys = array_column($reg->publicPortalLinks(), 'key');

        $this->assertSame(['status', 'edit'], $keys);
    }
}
