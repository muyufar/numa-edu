<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNilaiBulkRequest;
use App\Http\Requests\StoreNilaiRequest;
use App\Http\Requests\UpdateNilaiRequest;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NilaiController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Nilai::class);

        $kelasId = request('kelas_id');
        $mapelId = request('mata_pelajaran_id');
        $semester = request('semester');
        $tahunAjaran = request('tahun_ajaran');

        $nilais = Nilai::query()
            ->with(['siswa:id,nama,nis', 'mataPelajaran:id,kode,nama', 'kelas:id,tingkat,nama'])
            ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId))
            ->when($mapelId, fn ($q) => $q->where('mata_pelajaran_id', $mapelId))
            ->when($semester, fn ($q) => $q->where('semester', $semester))
            ->when($tahunAjaran, fn ($q) => $q->where('tahun_ajaran', $tahunAjaran))
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->orderBy('kelas_id')
            ->orderBy('mata_pelajaran_id')
            ->paginate(25)
            ->withQueryString();

        $filterKelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $filterMapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);

        $tahunFilterOptions = Kelas::query()
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        return view('nilai.index', compact(
            'nilais',
            'filterKelasOptions',
            'filterMapelOptions',
            'tahunFilterOptions',
            'kelasId',
            'mapelId',
            'semester',
            'tahunAjaran'
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', Nilai::class);

        $kelasId = request('kelas_id');

        $siswaOptions = $kelasId
            ? Siswa::query()->where('kelas_id', $kelasId)->orderBy('nama')->get(['id', 'nama', 'nis'])
            : collect();

        return view('nilai.create', array_merge(
            $this->formSelects(),
            compact('kelasId', 'siswaOptions')
        ));
    }

    public function store(StoreNilaiRequest $request): RedirectResponse
    {
        Nilai::query()->create($request->validated());

        return redirect()
            ->route('nilai.index')
            ->with('status', __('Nilai berhasil ditambahkan.'));
    }

    public function edit(Nilai $nilai): View
    {
        Gate::authorize('update', $nilai);

        $nilai->load(['siswa:id,nama,nis,kelas_id']);

        $siswaOptions = Siswa::query()
            ->where('kelas_id', $nilai->kelas_id)
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis']);

        $selects = $this->formSelects();
        $tahunOpts = $selects['tahunAjaranOptions'];
        if ($nilai->tahun_ajaran && ! $tahunOpts->contains($nilai->tahun_ajaran)) {
            $tahunOpts = $tahunOpts->prepend($nilai->tahun_ajaran)->values();
        }
        $selects['tahunAjaranOptions'] = $tahunOpts;

        return view('nilai.edit', array_merge(
            ['nilai' => $nilai],
            $selects,
            compact('siswaOptions')
        ));
    }

    public function update(UpdateNilaiRequest $request, Nilai $nilai): RedirectResponse
    {
        $nilai->update($request->validated());

        return redirect()
            ->route('nilai.index')
            ->with('status', __('Nilai berhasil diperbarui.'));
    }

    public function destroy(Nilai $nilai): RedirectResponse
    {
        Gate::authorize('delete', $nilai);

        $nilai->delete();

        return redirect()
            ->route('nilai.index')
            ->with('status', __('Nilai berhasil dihapus.'));
    }

    public function bulkCreate(): View
    {
        Gate::authorize('create', Nilai::class);

        $kelasId = request('kelas_id');
        $mapelId = request('mata_pelajaran_id');
        $semester = request('semester', '1');
        $tahunAjaran = request('tahun_ajaran');

        $siswas = collect();
        $existing = collect();

        if ($kelasId && $mapelId && $tahunAjaran) {
            $siswas = Siswa::query()
                ->where('kelas_id', $kelasId)
                ->orderBy('nama')
                ->get(['id', 'nama', 'nis']);

            $existing = Nilai::query()
                ->where('kelas_id', $kelasId)
                ->where('mata_pelajaran_id', $mapelId)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->whereIn('siswa_id', $siswas->pluck('id'))
                ->get()
                ->keyBy('siswa_id');
        }

        return view('nilai.bulk', array_merge(
            $this->formSelects(),
            compact('kelasId', 'mapelId', 'semester', 'tahunAjaran', 'siswas', 'existing')
        ));
    }

    public function bulkStore(StoreNilaiBulkRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            foreach ($data['nilai'] as $row) {
                Nilai::query()->updateOrCreate(
                    [
                        'siswa_id' => $row['siswa_id'],
                        'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                        'semester' => $data['semester'],
                        'tahun_ajaran' => $data['tahun_ajaran'],
                    ],
                    [
                        'kelas_id' => $data['kelas_id'],
                        'nilai_akhir' => $row['nilai_akhir'] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('nilai.bulk.create', [
                'kelas_id' => $data['kelas_id'],
                'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                'semester' => $data['semester'],
                'tahun_ajaran' => $data['tahun_ajaran'],
            ])
            ->with('status', __('Nilai tersimpan.'));
    }

    /**
     * @return array{kelasOptions: \Illuminate\Database\Eloquent\Collection, mapelOptions: \Illuminate\Database\Eloquent\Collection, tahunAjaranOptions: \Illuminate\Support\Collection}
     */
    private function formSelects(): array
    {
        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $mapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);

        $tahunAjaranOptions = Kelas::query()
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        return compact('kelasOptions', 'mapelOptions', 'tahunAjaranOptions');
    }
}
