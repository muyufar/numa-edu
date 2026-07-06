<?php

namespace App\Support;

use App\Models\Scopes\TenantScope;
use App\Models\Sekolah;

class SekolahPresensiSettings
{
    public const MODE_HARIAN = 'harian';

    public const MODE_PER_MAPEL = 'per_mapel';

    /** @var list<string> */
    public const MODE_OPTIONS = [
        self::MODE_HARIAN,
        self::MODE_PER_MAPEL,
    ];

    public static function resolveSekolah(?int $sekolahId = null): ?Sekolah
    {
        if ($sekolahId !== null) {
            return Sekolah::withoutGlobalScopes()->find($sekolahId);
        }

        $effectiveId = TenantScope::effectiveSekolahId();

        if ($effectiveId === false || $effectiveId === null) {
            return null;
        }

        return Sekolah::withoutGlobalScopes()->find((int) $effectiveId);
    }

    public static function siswaMode(?Sekolah $sekolah = null): string
    {
        $sekolah ??= self::resolveSekolah();

        $mode = $sekolah?->presensi_siswa_mode ?? self::MODE_HARIAN;

        return in_array($mode, self::MODE_OPTIONS, true) ? $mode : self::MODE_HARIAN;
    }

    public static function isPerMapel(?Sekolah $sekolah = null): bool
    {
        return self::siswaMode($sekolah) === self::MODE_PER_MAPEL;
    }

    public static function slotForJadwal(?int $jadwalId): string
    {
        return $jadwalId ? 'j:'.$jadwalId : 'harian';
    }

    /**
     * @return array<string, string>
     */
    public static function modeLabels(): array
    {
        return [
            self::MODE_HARIAN => __('Presensi harian (1x per hari)'),
            self::MODE_PER_MAPEL => __('Presensi per mapel (per jadwal pelajaran)'),
        ];
    }
}
