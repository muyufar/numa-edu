<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJadwalRequest;
use App\Http\Requests\UpdateJadwalRequest;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Jadwal::class);

        $tahunAjaran = request('tahun_ajaran');
        $kelasId = request('kelas_id');

        $jadwals = Jadwal::query()
            ->with(['kelas:id,tingkat,nama,tahun_ajaran', 'mataPelajaran:id,kode,nama', 'guru:id,nama'])
            ->when($tahunAjaran, fn ($q) => $q->where('tahun_ajaran', $tahunAjaran))
            ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId))
            ->ordered()
            ->paginate(20)
            ->withQueryString();

        $filterKelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $tahunFilterOptions = Kelas::query()
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        return view('jadwal.index', compact(
            'jadwals',
            'filterKelasOptions',
            'tahunFilterOptions',
            'tahunAjaran',
            'kelasId'
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', Jadwal::class);

        return view('jadwal.create', $this->formOptions(null));
    }

    public function store(StoreJadwalRequest $request): RedirectResponse
    {
        Jadwal::query()->create($request->validated());

        return redirect()
            ->route('jadwal.index')
            ->with('status', __('Jadwal berhasil ditambahkan.'));
    }

    public function edit(Jadwal $jadwal): View
    {
        Gate::authorize('update', $jadwal);

        return view('jadwal.edit', array_merge(
            ['jadwal' => $jadwal],
            $this->formOptions($jadwal)
        ));
    }

    public function update(UpdateJadwalRequest $request, Jadwal $jadwal): RedirectResponse
    {
        $jadwal->update($request->validated());

        return redirect()
            ->route('jadwal.index')
            ->with('status', __('Jadwal berhasil diperbarui.'));
    }

    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        Gate::authorize('delete', $jadwal);

        $jadwal->delete();

        return redirect()
            ->route('jadwal.index')
            ->with('status', __('Jadwal berhasil dihapus.'));
    }

    /**
     * @return array{kelasOptions: \Illuminate\Database\Eloquent\Collection, mapelOptions: \Illuminate\Database\Eloquent\Collection, guruOptions: \Illuminate\Database\Eloquent\Collection, tahunAjaranOptions: \Illuminate\Support\Collection}
     */
    private function formOptions(?Jadwal $jadwal): array
    {
        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $mapelOptions = MataPelajaran::query()
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama']);

        $guruOptions = Guru::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip']);

        $tahunAjaranOptions = Kelas::query()
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        if ($jadwal?->tahun_ajaran && ! $tahunAjaranOptions->contains($jadwal->tahun_ajaran)) {
            $tahunAjaranOptions = $tahunAjaranOptions->prepend($jadwal->tahun_ajaran)->values();
        }

        return compact('kelasOptions', 'mapelOptions', 'guruOptions', 'tahunAjaranOptions');
    }
}
