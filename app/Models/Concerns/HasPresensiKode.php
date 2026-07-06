<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasPresensiKode
{
    public static function bootHasPresensiKode(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getAttribute('presensi_kode')) {
                return;
            }

            $prefix = static::presensiKodePrefix();
            do {
                $kode = 'NUMA-'.$prefix.'-'.strtoupper(Str::random(12));
            } while (static::withoutGlobalScopes()->where('presensi_kode', $kode)->exists());

            $model->setAttribute('presensi_kode', $kode);
        });
    }

    abstract protected static function presensiKodePrefix(): string;
}
