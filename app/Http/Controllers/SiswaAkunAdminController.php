<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaAkunAdminController extends Controller
{
    public function index(Request $request): View
    {
        $auth = $request->user();
        abort_unless($auth?->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang']), 403);

        $q = trim((string) $request->query('q', ''));
        $onlyMissing = (string) $request->query('only_missing', '') === '1';

        $sekolahId = null;
        $sekolahOptions = [];

        if ($auth->hasRole('super_admin')) {
            $sekolahOptions = Sekolah::withoutGlobalScopes()
                ->where('is_active', true)
                ->orderBy('nama')
                ->get(['id', 'nama', 'npsn'])
                ->all();

            $sekolahId = $request->query('sekolah_id');
            $sekolahId = $sekolahId !== null && $sekolahId !== '' ? (int) $sekolahId : null;
        } elseif ($auth->hasRole('pengurus_cabang')) {
            $sekolahId = (int) session('pengurus_sekolah_id');
            abort_unless($sekolahId, 403);
        } else {
            $sekolahId = (int) $auth->sekolah_id;
            abort_unless($sekolahId, 403);
        }

        $siswas = Siswa::withoutGlobalScopes()
            ->when($sekolahId !== null, fn (Builder $b) => $b->where('sekolah_id', $sekolahId))
            ->when($q !== '', function (Builder $b) use ($q) {
                $b->where(function (Builder $s) use ($q) {
                    $s->where('nama', 'like', '%'.$q.'%')
                        ->orWhere('nis', 'like', '%'.$q.'%')
                        ->orWhere('nisn', 'like', '%'.$q.'%');
                });
            })
            ->when($onlyMissing, fn (Builder $b) => $b->whereNull('user_id'))
            ->with(['kelas:id,tingkat,nama,tahun_ajaran', 'user:id,email'])
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('siswa-akun-admin.index', [
            'siswas' => $siswas,
            'q' => $q,
            'onlyMissing' => $onlyMissing,
            'sekolahId' => $sekolahId,
            'sekolahOptions' => $sekolahOptions,
        ]);
    }
}

