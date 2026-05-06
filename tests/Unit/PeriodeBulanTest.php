<?php

namespace Tests\Unit;

use App\Support\PeriodeBulan;
use PHPUnit\Framework\TestCase;

class PeriodeBulanTest extends TestCase
{
    public function test_order_months_swaps_when_from_greater_than_to(): void
    {
        [$a, $b] = PeriodeBulan::orderMonths('2026-06', '2026-01');

        $this->assertSame('2026-01', $a);
        $this->assertSame('2026-06', $b);
    }

    public function test_order_months_unchanged_when_from_less_or_equal(): void
    {
        [$a, $b] = PeriodeBulan::orderMonths('2026-01', '2026-06');

        $this->assertSame('2026-01', $a);
        $this->assertSame('2026-06', $b);

        [$c, $d] = PeriodeBulan::orderMonths('2026-03', '2026-03');

        $this->assertSame('2026-03', $c);
        $this->assertSame('2026-03', $d);
    }
}
