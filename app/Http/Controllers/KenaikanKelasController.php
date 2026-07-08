<?php

namespace App\Http\Controllers;

use App\Http\Requests\GraduateSiswaKelasRequest;
use App\Http\Requests\PromoteSiswaKelasRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Services\SiswaKenaikanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KenaikanKelasController extends Controller
{
    public function __construct(
        private readonly SiswaKenaikanService $kenaikanService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('create', Siswa::class);

        $kelasAsalId = $request->filled('kelas_asal_id') ? (int) $request->input('kelas_asal_id') : null;

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasAsal = null;
        $siswas = collect();

        if ($kelasAsalId) {
            $kelasAsal = Kelas::query()->findOrFail($kelasAsalId);

            $siswas = Siswa::query()
                ->where('kelas_id', $kelasAsal->id)
                ->bukanAlumni()
                ->orderBy('nama')
                ->get(['id', 'nis', 'nisn', 'nama', 'status', 'jenis_kelamin']);
        }

        $kelasTujuanOptions = $kelasOptions->filter(
            fn (Kelas $k) => ! $kelasAsal || (int) $k->id !== (int) $kelasAsal->id
        )->values();

        return view('siswa.kenaikan-kelas.index', compact(
            'kelasOptions',
            'kelasAsalId',
            'kelasAsal',
            'siswas',
            'kelasTujuanOptions',
        ));
    }

    public function promote(PromoteSiswaKelasRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $asal = Kelas::query()->findOrFail((int) $data['kelas_asal_id']);
        $tujuan = Kelas::query()->findOrFail((int) $data['kelas_tujuan_id']);
        $siswaIds = array_map('intval', $data['siswa_ids']);

        $count = $this->kenaikanService->promote($asal, $tujuan, $siswaIds);

        return redirect()
            ->route('siswa.kenaikan-kelas.index', ['kelas_asal_id' => $asal->id])
            ->with('status', __(':count siswa berhasil dinaikkan ke :kelas.', [
                'count' => $count,
                'kelas' => $tujuan->tingkat.' '.$tujuan->nama.' · '.$tujuan->tahun_ajaran,
            ]));
    }

    public function graduate(GraduateSiswaKelasRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $kelas = Kelas::query()->findOrFail((int) $data['kelas_id']);
        $siswaIds = array_map('intval', $data['siswa_ids']);

        $count = $this->kenaikanService->graduate($kelas, $siswaIds, $data['status']);

        return redirect()
            ->route('siswa.alumni.index')
            ->with('status', __(':count siswa berhasil diluluskan dengan status :status.', [
                'count' => $count,
                'status' => $data['status'],
            ]));
    }
}
