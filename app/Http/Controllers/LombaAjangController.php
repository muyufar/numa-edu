<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLombaAjangRequest;
use App\Http\Requests\UpdateLombaAjangRequest;
use App\Models\Kelas;
use App\Models\LombaAjang;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LombaAjangController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', LombaAjang::class);

        $rows = LombaAjang::query()
            ->withCount('pesertas')
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->paginate(25);

        return view('kesiswaan.lomba.index', compact('rows'));
    }

    public function create(): View
    {
        Gate::authorize('create', LombaAjang::class);

        return view('kesiswaan.lomba.create', ['row' => new LombaAjang()]);
    }

    public function store(StoreLombaAjangRequest $request): RedirectResponse
    {
        LombaAjang::query()->create($request->validated());

        return redirect()
            ->route('kesiswaan.lomba.index')
            ->with('status', __('Lomba / ajang ditambahkan.'));
    }

    public function edit(LombaAjang $lomba_ajang): View
    {
        Gate::authorize('update', $lomba_ajang);

        $lomba_ajang->load(['pesertas.siswa:id,nama,nis,kelas_id', 'pesertas.siswa.kelas:id,tingkat,nama']);

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasId = old('kelas_id', request('kelas_id'));
        $siswas = $kelasId
            ? Siswa::query()->where('kelas_id', $kelasId)->orderBy('nama')->get(['id', 'nama', 'nis'])
            : collect();

        $selectedSiswaIds = old('siswa_ids', $lomba_ajang->pesertas->pluck('siswa_id')->all());

        return view('kesiswaan.lomba.edit', compact('lomba_ajang', 'kelasOptions', 'kelasId', 'siswas', 'selectedSiswaIds'));
    }

    public function update(UpdateLombaAjangRequest $request, LombaAjang $lomba_ajang): RedirectResponse
    {
        $data = collect($request->validated())->except('siswa_ids')->all();
        $lomba_ajang->update($data);
        $this->syncPeserta($lomba_ajang, $request->input('siswa_ids', []));

        return redirect()
            ->route('kesiswaan.lomba.edit', $lomba_ajang)
            ->with('status', __('Lomba / ajang diperbarui.'));
    }

    public function destroy(LombaAjang $lomba_ajang): RedirectResponse
    {
        Gate::authorize('delete', $lomba_ajang);

        $lomba_ajang->delete();

        return redirect()
            ->route('kesiswaan.lomba.index')
            ->with('status', __('Lomba / ajang dihapus.'));
    }

    /**
     * @param  list<int|string>|null  $siswaIds
     */
    private function syncPeserta(LombaAjang $lomba, ?array $siswaIds): void
    {
        $siswaIds = array_values(array_unique(array_map('intval', array_filter($siswaIds ?? []))));
        $lomba->pesertas()->whereNotIn('siswa_id', $siswaIds)->delete();

        $existing = $lomba->pesertas()->pluck('siswa_id')->all();
        foreach ($siswaIds as $siswaId) {
            if (! in_array($siswaId, $existing, true)) {
                $lomba->pesertas()->create(['siswa_id' => $siswaId]);
            }
        }
    }
}
