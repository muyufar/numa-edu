<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 403);

        $q = trim((string) $request->query('q', ''));
        $qMin = mb_strlen($q) >= 2;

        $siswas = collect();
        $kelas = collect();
        $tagihans = collect();

        if ($qMin) {
            if ($user->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang'])) {
                $siswas = Siswa::query()
                    ->with('kelas')
                    ->where(function ($qq) use ($q) {
                        $qq->where('nama', 'like', '%'.$q.'%')
                            ->orWhere('nis', 'like', '%'.$q.'%')
                            ->orWhere('nisn', 'like', '%'.$q.'%');
                    })
                    ->orderBy('nama')
                    ->limit(10)
                    ->get();

                $kelas = Kelas::query()
                    ->where(function ($qq) use ($q) {
                        $qq->where('nama', 'like', '%'.$q.'%')
                            ->orWhere('tahun_ajaran', 'like', '%'.$q.'%')
                            ->orWhere('tingkat', 'like', '%'.$q.'%');
                    })
                    ->orderByDesc('is_active')
                    ->orderBy('tingkat')
                    ->orderBy('nama')
                    ->limit(10)
                    ->get();
            }

            if ($user->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang'])) {
                $tagihans = Tagihan::query()
                    ->with('siswa')
                    ->where(function ($qq) use ($q) {
                        $qq->where('jenis', 'like', '%'.$q.'%')
                            ->orWhere('periode', 'like', '%'.$q.'%')
                            ->orWhereHas('siswa', function ($qs) use ($q) {
                                $qs->where('nama', 'like', '%'.$q.'%')
                                    ->orWhere('nis', 'like', '%'.$q.'%')
                                    ->orWhere('nisn', 'like', '%'.$q.'%');
                            });
                    })
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();
            }
        }

        return view('search.index', compact('q', 'qMin', 'siswas', 'kelas', 'tagihans'));
    }
}
