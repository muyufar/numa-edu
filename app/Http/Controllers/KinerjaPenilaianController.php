<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKinerjaPenilaianRequest;
use App\Http\Requests\UpdateKinerjaPenilaianRequest;
use App\Models\Guru;
use App\Models\KinerjaPenilaian;
use App\Models\Pegawai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KinerjaPenilaianController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', KinerjaPenilaian::class);

        $query = KinerjaPenilaian::query()
            ->with([
                'guru:id,nama',
                'pegawai:id,nama',
                'dibuatOleh:id,name',
            ])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($t = request('target_type')) {
            if (in_array($t, KinerjaPenilaian::TARGET_TYPES, true)) {
                $query->where('target_type', $t);
            }
        }

        if ($p = request('periode')) {
            $query->where('periode', $p);
        }

        if ($q = trim((string) request('q'))) {
            $query->where(function ($w) use ($q) {
                $w->whereHas('guru', fn ($g) => $g->where('nama', 'like', "%{$q}%"))
                    ->orWhereHas('pegawai', fn ($p) => $p->where('nama', 'like', "%{$q}%"))
                    ->orWhere('aspek', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(15)->withQueryString();

        return view('kinerja.index', compact('items'));
    }

    public function create(): View
    {
        Gate::authorize('create', KinerjaPenilaian::class);

        $gurus = Guru::query()->orderBy('nama')->get(['id', 'nama']);
        $pegawais = Pegawai::query()->orderBy('nama')->get(['id', 'nama']);

        return view('kinerja.create', compact('gurus', 'pegawais'));
    }

    public function store(StoreKinerjaPenilaianRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['dibuat_oleh'] = auth()->id();

        KinerjaPenilaian::query()->create($data);

        return redirect()
            ->route('kinerja.index')
            ->with('status', __('Penilaian kinerja ditambahkan.'));
    }

    public function edit(KinerjaPenilaian $kinerja_penilaian): View
    {
        Gate::authorize('update', $kinerja_penilaian);

        $gurus = Guru::query()->orderBy('nama')->get(['id', 'nama']);
        $pegawais = Pegawai::query()->orderBy('nama')->get(['id', 'nama']);

        return view('kinerja.edit', [
            'item' => $kinerja_penilaian,
            'gurus' => $gurus,
            'pegawais' => $pegawais,
        ]);
    }

    public function update(UpdateKinerjaPenilaianRequest $request, KinerjaPenilaian $kinerja_penilaian): RedirectResponse
    {
        $kinerja_penilaian->update($request->validated());

        return redirect()
            ->route('kinerja.index')
            ->with('status', __('Penilaian kinerja diperbarui.'));
    }

    public function destroy(KinerjaPenilaian $kinerja_penilaian): RedirectResponse
    {
        Gate::authorize('delete', $kinerja_penilaian);

        $kinerja_penilaian->delete();

        return redirect()
            ->route('kinerja.index')
            ->with('status', __('Penilaian kinerja dihapus.'));
    }
}

