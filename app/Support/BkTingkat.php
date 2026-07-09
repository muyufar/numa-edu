<?php

namespace App\Support;

final class BkTingkat
{
    /** @var list<string> */
    public const OPTIONS = ['ringan', 'sedang', 'berat'];

    public static function label(string $tingkat): string
    {
        return match ($tingkat) {
            'ringan' => __('Ringan'),
            'sedang' => __('Sedang'),
            'berat' => __('Berat'),
            default => $tingkat,
        };
    }
}
