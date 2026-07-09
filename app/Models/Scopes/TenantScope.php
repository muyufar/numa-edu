<?php

namespace App\Models\Scopes;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $id = self::effectiveSekolahId();

        if ($id === null) {
            return;
        }

        if ($id === false) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->where($model->getTable().'.sekolah_id', $id);
    }

    /**
     * @return int|false|null null = tanpa filter (super_admin); false = blokir hasil; int = filter sekolah
     */
    public static function effectiveSekolahId(): int|false|null
    {
        if (! Auth::check()) {
            return (int) config('tenancy.default_sekolah_id', 1);
        }

        $user = Auth::user();
        if ($user->hasRole('super_admin')) {
            return null;
        }

        if ($user->hasRole('pengurus_cabang')) {
            $sid = session('pengurus_sekolah_id');

            return $sid ? (int) $sid : false;
        }

        if ($user->sekolah_id) {
            return (int) $user->sekolah_id;
        }

        return self::resolveSekolahIdFromProfile($user);
    }

    private static function resolveSekolahIdFromProfile(User $user): int|false
    {
        $guruSekolahId = Guru::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->value('sekolah_id');
        if ($guruSekolahId) {
            return (int) $guruSekolahId;
        }

        $siswaSekolahId = Siswa::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->value('sekolah_id');
        if ($siswaSekolahId) {
            return (int) $siswaSekolahId;
        }

        return false;
    }
}
