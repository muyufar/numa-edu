<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use App\Models\Sekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToSekolah
{
    public static function bootBelongsToSekolah(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model): void {
            if ($model->getAttribute('sekolah_id')) {
                return;
            }

            if (method_exists($model, 'resolveSekolahIdOnCreating')) {
                $resolved = $model->resolveSekolahIdOnCreating();
                if ($resolved !== null) {
                    $model->setAttribute('sekolah_id', $resolved);

                    return;
                }
            }

            $model->setAttribute('sekolah_id', static::defaultSekolahIdForNewRow());
        });
    }

    protected static function defaultSekolahIdForNewRow(): int
    {
        if (! Auth::check()) {
            return (int) config('tenancy.default_sekolah_id', 1);
        }

        $user = Auth::user();
        if ($user->hasRole('pengurus_cabang') && session('pengurus_sekolah_id')) {
            return (int) session('pengurus_sekolah_id');
        }

        if ($user->sekolah_id) {
            return (int) $user->sekolah_id;
        }

        return (int) config('tenancy.default_sekolah_id', 1);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }
}
