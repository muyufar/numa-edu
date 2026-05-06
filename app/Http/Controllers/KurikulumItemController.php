<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKurikulumItemRequest;
use App\Http\Requests\UpdateKurikulumItemRequest;
use App\Models\KurikulumItem;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KurikulumItemController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', KurikulumItem::class);

        $query = KurikulumItem::query()
            ->with('mataPelajaran:id,kode,nama')
            ->orderBy('tahun_ajaran', 'desc')
            ->orderBy('semester')
            ->orderBy('tingkat')
            ->orderBy('urutan')
            ->orderBy('id');

        if ($ta = trim((string) request('tahun_ajaran'))) {
            $query->where('tahun_ajaran', $ta);
        }
        if (($sem = request('semester')) !== null && $sem !== '') {
            $query->where('semester', $sem);
        }
        if (($tingkat = request('tingkat')) !== null && $tingkat !== '') {
            $query->where('tingkat', (int) $tingkat);
        }
        if ($mapelId = request('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $mapelId);
        }

        $items = $query->paginate(20)->withQueryString();

        $mapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);
        $tahunAjaranOptions = KurikulumItem::query()
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        return view('kurikulum.index', compact('items', 'mapelOptions', 'tahunAjaranOptions'));
    }

    public function create(): View
    {
        Gate::authorize('create', KurikulumItem::class);

        $mapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);

        return view('kurikulum.create', compact('mapelOptions'));
    }

    public function store(StoreKurikulumItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['urutan'] = $data['urutan'] ?? 0;

        KurikulumItem::query()->create($data);

        return redirect()
            ->route('kurikulum.index', array_filter([
                'tahun_ajaran' => $data['tahun_ajaran'],
                'semester' => $data['semester'],
            ]))
            ->with('status', __('Item kurikulum ditambahkan.'));
    }

    public function edit(KurikulumItem $kurikulum_item): View
    {
        Gate::authorize('update', $kurikulum_item);

        $mapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);

        return view('kurikulum.edit', [
            'item' => $kurikulum_item,
            'mapelOptions' => $mapelOptions,
        ]);
    }

    public function update(UpdateKurikulumItemRequest $request, KurikulumItem $kurikulum_item): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['urutan'] = $data['urutan'] ?? 0;

        $kurikulum_item->update($data);

        return redirect()
            ->route('kurikulum.index', array_filter([
                'tahun_ajaran' => $data['tahun_ajaran'],
                'semester' => $data['semester'],
            ]))
            ->with('status', __('Item kurikulum diperbarui.'));
    }

    public function destroy(KurikulumItem $kurikulum_item): RedirectResponse
    {
        Gate::authorize('delete', $kurikulum_item);

        $kurikulum_item->delete();

        return redirect()
            ->route('kurikulum.index')
            ->with('status', __('Item kurikulum dihapus.'));
    }
}
