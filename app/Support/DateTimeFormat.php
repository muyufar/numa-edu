<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class DateTimeFormat
{
    public const DATE = 'Y-m-d';
    public const DATETIME = 'Y-m-d H:i';
    public const DATETIME_LOCAL = 'Y-m-d\TH:i';

    public static function date(CarbonInterface|string|null $value, string $fallback = '—'): string
    {
        $c = self::toCarbon($value);

        return $c ? $c->format(self::DATE) : $fallback;
    }

    public static function datetime(CarbonInterface|string|null $value, string $fallback = '—'): string
    {
        $c = self::toCarbon($value);

        return $c ? $c->format(self::DATETIME) : $fallback;
    }

    public static function datetimeLocalValue(CarbonInterface|string|null $value): string
    {
        $c = self::toCarbon($value);

        return $c ? $c->format(self::DATETIME_LOCAL) : '';
    }

    private static function toCarbon(CarbonInterface|string|null $value): ?CarbonInterface
    {
        if ($value instanceof CarbonInterface) {
            return $value;
        }

        if (! is_string($value)) {
            return null;
        }

        $raw = trim($value);
        if ($raw === '') {
            return null;
        }

        try {
            // Pastikan interpretasi input mengikuti timezone aplikasi
            return Carbon::parse($raw, config('app.timezone'));
        } catch (\Throwable) {
            return null;
        }
    }
}

