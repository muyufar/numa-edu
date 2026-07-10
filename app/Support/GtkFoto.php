<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class GtkFoto
{
    public static function url(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return '/storage/'.$path;
    }

    /**
     * @return array{foto_path: string, foto_name: string}
     */
    public static function store(Model $model, UploadedFile $file): array
    {
        $folder = $model instanceof \App\Models\Guru ? 'gtk/guru' : 'gtk/pegawai';

        if ($model->getAttribute('foto_path')) {
            self::delete($model->getAttribute('foto_path'));
        }

        return [
            'foto_path' => $file->store($folder, 'public'),
            'foto_name' => $file->getClientOriginalName(),
        ];
    }

    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
