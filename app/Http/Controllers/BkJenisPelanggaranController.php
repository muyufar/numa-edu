<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBkJenisPelanggaranRequest;
use App\Http\Requests\UpdateBkJenisPelanggaranRequest;
use App\Models\BkJenisPelanggaran;
use App\Services\BkMasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BkJenisPelanggaranController extends Controller
{
    public function index(BkMasterDataService $masterData): View
    {
        Gate::authorize('viewAny', BkJenisPelanggaran::class);

        $masterData->ensureForCurrentTenant();

        $rows = BkJenisPelanggaran::query()
            ->orderBy('nama')
            ->paginate(25);

        return view('bk.jenis-pelanggaran.index', compact('rows'));
    }

    public function create(): View
    {
        Gate::authorize('create', BkJenisPelanggaran::class);

        return view('bk.jenis-pelanggaran.create', ['row' => new BkJenisPelanggaran()]);
    }

    public function store(StoreBkJenisPelanggaranRequest $request): RedirectResponse
    {
        BkJenisPelanggaran::query()->create($request->validated());

        return redirect()
            ->route('bk.jenis-pelanggaran.index')
            ->with('status', __('Jenis pelanggaran ditambahkan.'));
    }

    public function edit(BkJenisPelanggaran $bk_jenis_pelanggaran): View
    {
        Gate::authorize('update', $bk_jenis_pelanggaran);

        return view('bk.jenis-pelanggaran.edit', ['row' => $bk_jenis_pelanggaran]);
    }

    public function update(UpdateBkJenisPelanggaranRequest $request, BkJenisPelanggaran $bk_jenis_pelanggaran): RedirectResponse
    {
        $bk_jenis_pelanggaran->update($request->validated());

        return redirect()
            ->route('bk.jenis-pelanggaran.index')
            ->with('status', __('Jenis pelanggaran diperbarui.'));
    }

    public function destroy(BkJenisPelanggaran $bk_jenis_pelanggaran): RedirectResponse
    {
        Gate::authorize('delete', $bk_jenis_pelanggaran);

        $bk_jenis_pelanggaran->delete();

        return redirect()
            ->route('bk.jenis-pelanggaran.index')
            ->with('status', __('Jenis pelanggaran dihapus.'));
    }
}
