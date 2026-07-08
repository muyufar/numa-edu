<?php

namespace App\Support;

use Illuminate\Support\Str;

class SiswaAkunEmail
{
    public const DOMAIN = 'numaedu.id';

    public static function fromNisn(?string $nisn): ?string
    {
        $normalized = self::normalizeNisn($nisn);
        if ($normalized === null) {
            return null;
        }

        return $normalized.'@'.self::DOMAIN;
    }

    public static function normalizeNisn(?string $nisn): ?string
    {
        $value = Str::lower(preg_replace('/\s+/', '', trim((string) $nisn)) ?? '');

        return $value !== '' ? $value : null;
    }
}
