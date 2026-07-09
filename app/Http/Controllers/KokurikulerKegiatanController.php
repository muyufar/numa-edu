<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKokurikulerKegiatanRequest;
use App\Http\Requests\UpdateKokurikulerKegiatanRequest;
use App\Models\Kelas;
use App\Models\KokurikulerKegiatan;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KokurikulerKegiatanController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', KokurikulerKegiatan::class);

        $rows = KokurikulerKegiatan::query()
            ->with(['kelas:id,tingkat,nama,tahun_ajaran'])
            ->withCount('anggotas')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(25);

        return view('kesiswaan.kokurikuler.index', compact('rows'));
    }

    public function create(): View
    {
        Gate::authorize('create', KokurikulerKegiatan::class);

        return view('kesiswaan.kokurikuler.create', $this->formContext());
    }

    public function store(StoreKokurikulerKegiatanRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except(['siswa_ids', 'lkpd'])->all();
        $data['dicatat_oleh'] = $request->user()->id;

        if ($request->hasFile('lkpd')) {
            $file = $request->file('lkpd');
            $data['lkpd_path'] = $file->store('kokurikuler/lkpd', 'public');
            $data['lkpd_name'] = $file->getClientOriginalName();
        }

        $kegiatan = KokurikulerKegiatan::query()->create($data);
        $this->syncAnggota($kegiatan, $request->input('siswa_ids', []));

        return redirect()
            ->route('kesiswaan.kokurikuler.index')
            ->with('status', __('Kegiatan kokurikuler dicatat.'));
    }

    public function edit(KokurikulerKegiatan $kokurikuler_kegiatan): View
    {
        Gate::authorize('update', $kokurikuler_kegiatan);

        $kokurikuler_kegiatan->load(['anggotas.siswa:id,nama,nis', 'kelas:id,tingkat,nama,tahun_ajaran']);

        return view('kesiswaan.kokurikuler.edit', array_merge(
            ['row' => $kokurikuler_kegiatan],
            $this->formContext($kokurikuler_kegiatan),
        ));
    }

    public function update(UpdateKokurikulerKegiatanRequest $request, KokurikulerKegiatan $kokurikuler_kegiatan): RedirectResponse
    {
        $data = collect($request->validated())->except(['siswa_ids', 'lkpd'])->all();

        if ($request->hasFile('lkpd')) {
            if ($kokurikuler_kegiatan->lkpd_path) {
                Storage::disk('public')->delete($kokurikuler_kegiatan->lkpd_path);
            }

            $file = $request->file('lkpd');
            $data['lkpd_path'] = $file->store('kokurikuler/lkpd', 'public');
            $data['lkpd_name'] = $file->getClientOriginalName();
        }

        $kokurikuler_kegiatan->update($data);
        $this->syncAnggota($kokurikuler_kegiatan, $request->input('siswa_ids', []));

        return redirect()
            ->route('kesiswaan.kokurikuler.edit', $kokurikuler_kegiatan)
            ->with('status', __('Kegiatan kokurikuler diperbarui.'));
    }

    public function destroy(KokurikulerKegiatan $kokurikuler_kegiatan): RedirectResponse
    {
        Gate::authorize('delete', $kokurikuler_kegiatan);

        if ($kokurikuler_kegiatan->lkpd_path) {
            Storage::disk('public')->delete($kokurikuler_kegiatan->lkpd_path);
        }

        $kokurikuler_kegiatan->delete();

        return redirect()
            ->route('kesiswaan.kokurikuler.index')
            ->with('status', __('Kegiatan kokurikuler dihapus.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formContext(?KokurikulerKegiatan $row = null): array
    {
        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasId = old('kelas_id', request('kelas_id', $row?->kelas_id));
        $siswas = $kelasId
            ? Siswa::query()->where('kelas_id', $kelasId)->orderBy('nama')->get(['id', 'nama', 'nis'])
            : collect();

        $selectedSiswaIds = old('siswa_ids', $row?->anggotas?->pluck('siswa_id')->all() ?? []);

        return compact('kelasOptions', 'kelasId', 'siswas', 'selectedSiswaIds');
    }

    /**
     * @param  list<int|string>|null  $siswaIds
     */
    private function syncAnggota(KokurikulerKegiatan $kegiatan, ?array $siswaIds): void
    {
        $siswaIds = array_values(array_unique(array_map('intval', array_filter($siswaIds ?? []))));
        $kegiatan->anggotas()->whereNotIn('siswa_id', $siswaIds)->delete();

        $existing = $kegiatan->anggotas()->pluck('siswa_id')->all();
        foreach ($siswaIds as $siswaId) {
            if (! in_array($siswaId, $existing, true)) {
                $kegiatan->anggotas()->create(['siswa_id' => $siswaId]);
            }
        }
    }
}
