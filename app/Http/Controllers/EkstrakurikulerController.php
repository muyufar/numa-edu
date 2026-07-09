<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEkstrakurikulerKegiatanRequest;
use App\Http\Requests\StoreEkstrakurikulerRequest;
use App\Http\Requests\UpdateEkstrakurikulerRequest;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerKegiatan;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EkstrakurikulerController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Ekstrakurikuler::class);

        $rows = Ekstrakurikuler::query()
            ->with(['guru:id,nama'])
            ->withCount('anggotas')
            ->orderBy('nama')
            ->paginate(25);

        return view('kesiswaan.ekstrakurikuler.index', compact('rows'));
    }

    public function create(): View
    {
        Gate::authorize('create', Ekstrakurikuler::class);

        return view('kesiswaan.ekstrakurikuler.create', [
            'row' => new Ekstrakurikuler(),
            'guruOptions' => $this->guruOptions(),
        ]);
    }

    public function store(StoreEkstrakurikulerRequest $request): RedirectResponse
    {
        Ekstrakurikuler::query()->create($request->validated());

        return redirect()
            ->route('kesiswaan.ekstrakurikuler.index')
            ->with('status', __('Ekstrakurikuler ditambahkan.'));
    }

    public function edit(Ekstrakurikuler $ekstrakurikuler): View
    {
        Gate::authorize('update', $ekstrakurikuler);

        $ekstrakurikuler->load([
            'anggotas.siswa:id,nama,nis',
            'kegiatans' => fn ($q) => $q->orderByDesc('tanggal')->limit(20),
            'kegiatans.dicatatOleh:id,name',
        ]);

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

        $selectedSiswaIds = old('siswa_ids', $ekstrakurikuler->anggotas->pluck('siswa_id')->all());

        return view('kesiswaan.ekstrakurikuler.edit', [
            'row' => $ekstrakurikuler,
            'guruOptions' => $this->guruOptions(),
            'kelasOptions' => $kelasOptions,
            'kelasId' => $kelasId,
            'siswas' => $siswas,
            'selectedSiswaIds' => $selectedSiswaIds,
        ]);
    }

    public function update(UpdateEkstrakurikulerRequest $request, Ekstrakurikuler $ekstrakurikuler): RedirectResponse
    {
        $data = collect($request->validated())->except('siswa_ids')->all();
        $ekstrakurikuler->update($data);
        $this->syncAnggota($ekstrakurikuler, $request->input('siswa_ids', []));

        return redirect()
            ->route('kesiswaan.ekstrakurikuler.edit', $ekstrakurikuler)
            ->with('status', __('Ekstrakurikuler diperbarui.'));
    }

    public function destroy(Ekstrakurikuler $ekstrakurikuler): RedirectResponse
    {
        Gate::authorize('delete', $ekstrakurikuler);

        $ekstrakurikuler->delete();

        return redirect()
            ->route('kesiswaan.ekstrakurikuler.index')
            ->with('status', __('Ekstrakurikuler dihapus.'));
    }

    public function storeKegiatan(StoreEkstrakurikulerKegiatanRequest $request, Ekstrakurikuler $ekstrakurikuler): RedirectResponse
    {
        Gate::authorize('update', $ekstrakurikuler);

        $ekstrakurikuler->kegiatans()->create([
            ...$request->validated(),
            'dicatat_oleh' => $request->user()->id,
        ]);

        return redirect()
            ->route('kesiswaan.ekstrakurikuler.edit', $ekstrakurikuler)
            ->with('status', __('Kegiatan ditambahkan.'));
    }

    public function destroyKegiatan(Ekstrakurikuler $ekstrakurikuler, EkstrakurikulerKegiatan $ekstrakurikuler_kegiatan): RedirectResponse
    {
        Gate::authorize('update', $ekstrakurikuler);

        if ((int) $ekstrakurikuler_kegiatan->ekstrakurikuler_id !== (int) $ekstrakurikuler->id) {
            abort(404);
        }

        $ekstrakurikuler_kegiatan->delete();

        return redirect()
            ->route('kesiswaan.ekstrakurikuler.edit', $ekstrakurikuler)
            ->with('status', __('Kegiatan dihapus.'));
    }

    /**
     * @param  list<int|string>|null  $siswaIds
     */
    private function syncAnggota(Ekstrakurikuler $ekskul, ?array $siswaIds): void
    {
        $siswaIds = array_values(array_unique(array_map('intval', array_filter($siswaIds ?? []))));
        $ekskul->anggotas()->whereNotIn('siswa_id', $siswaIds)->delete();

        $existing = $ekskul->anggotas()->pluck('siswa_id')->all();
        foreach ($siswaIds as $siswaId) {
            if (! in_array($siswaId, $existing, true)) {
                $ekskul->anggotas()->create(['siswa_id' => $siswaId]);
            }
        }
    }

    private function guruOptions()
    {
        return Guru::query()->orderBy('nama')->get(['id', 'nama']);
    }
}
