<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBkHomeVisitRequest;
use App\Http\Requests\UpdateBkHomeVisitRequest;
use App\Models\BkHomeVisit;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BkHomeVisitController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', BkHomeVisit::class);

        $kelasId = request('kelas_id');

        $rows = BkHomeVisit::query()
            ->with(['siswa.kelas:id,tingkat,nama,tahun_ajaran', 'dicatatOleh:id,name'])
            ->when($kelasId, fn ($q) => $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId)))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $filterKelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        return view('bk.home-visit.index', compact('rows', 'filterKelasOptions', 'kelasId'));
    }

    public function create(): View
    {
        Gate::authorize('create', BkHomeVisit::class);

        return view('bk.home-visit.create', $this->formContext());
    }

    public function store(StoreBkHomeVisitRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except(['kelas_id', 'foto'])->all();
        $data['dicatat_oleh'] = $request->user()->id;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $data['foto_path'] = $file->store('bk/home-visit', 'public');
            $data['foto_name'] = $file->getClientOriginalName();
        }

        BkHomeVisit::query()->create($data);

        return redirect()
            ->route('bk.home-visit.index')
            ->with('status', __('Home visit dicatat.'));
    }

    public function edit(BkHomeVisit $bk_home_visit): View
    {
        Gate::authorize('update', $bk_home_visit);

        $bk_home_visit->load(['siswa.kelas:id,tingkat,nama,tahun_ajaran']);

        return view('bk.home-visit.edit', array_merge(
            ['row' => $bk_home_visit],
            $this->formContext($bk_home_visit),
        ));
    }

    public function update(UpdateBkHomeVisitRequest $request, BkHomeVisit $bk_home_visit): RedirectResponse
    {
        $data = collect($request->validated())->except('foto')->all();

        if ($request->hasFile('foto')) {
            if ($bk_home_visit->foto_path) {
                Storage::disk('public')->delete($bk_home_visit->foto_path);
            }

            $file = $request->file('foto');
            $data['foto_path'] = $file->store('bk/home-visit', 'public');
            $data['foto_name'] = $file->getClientOriginalName();
        }

        $bk_home_visit->update($data);

        return redirect()
            ->route('bk.home-visit.edit', $bk_home_visit)
            ->with('status', __('Home visit diperbarui.'));
    }

    public function destroy(BkHomeVisit $bk_home_visit): RedirectResponse
    {
        Gate::authorize('delete', $bk_home_visit);

        if ($bk_home_visit->foto_path) {
            Storage::disk('public')->delete($bk_home_visit->foto_path);
        }

        $bk_home_visit->delete();

        return redirect()
            ->route('bk.home-visit.index')
            ->with('status', __('Home visit dihapus.'));
    }

    public function laporKepsek(BkHomeVisit $bk_home_visit): RedirectResponse
    {
        Gate::authorize('update', $bk_home_visit);

        $bk_home_visit->update([
            'dilaporkan_kepsek_at' => now(),
            'status' => 'dilaporkan',
        ]);

        return redirect()
            ->route('bk.home-visit.edit', $bk_home_visit)
            ->with('status', __('Laporan home visit dikirim ke kepala sekolah.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formContext(?BkHomeVisit $row = null): array
    {
        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasId = old('kelas_id', request('kelas_id', $row?->siswa?->kelas_id));
        $siswas = $kelasId
            ? Siswa::query()->where('kelas_id', $kelasId)->orderBy('nama')->get(['id', 'nama', 'nis'])
            : collect();

        if ($siswas->isEmpty() && $row?->siswa_id) {
            $siswas = Siswa::query()->whereKey($row->siswa_id)->get(['id', 'nama', 'nis']);
        }

        return compact('kelasOptions', 'kelasId', 'siswas');
    }
}
