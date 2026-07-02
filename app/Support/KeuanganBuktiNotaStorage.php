<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class KeuanganBuktiNotaStorage
{
    /** @var list<string> */
    public const ALLOWED_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public const MAX_KB = 5120;

    public static function store(?UploadedFile $file, int $sekolahId, string $folder): ?string
    {
        if ($file === null) {
            return null;
        }

        return $file->store("keuangan/{$folder}/sekolah-{$sekolahId}", 'public');
    }

    public static function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }

    public static function downloadName(?string $path, string $fallbackBase): string
    {
        if ($path !== null && $path !== '') {
            $base = basename($path);
            if ($base !== '') {
                return $base;
            }
        }

        return $fallbackBase;
    }
}
