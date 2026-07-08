<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventarisMutasiRequest;
use App\Http\Requests\UpdateInventarisMutasiRequest;
use App\Models\InventarisBarang;
use App\Models\InventarisMutasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InventarisMutasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', InventarisMutasi::class);

        $barangId = $request->query('barang_id');
        $tipe = $request->query('tipe');

        $mutasis = InventarisMutasi::query()
            ->with(['barang', 'dicatatOleh'])
            ->when($barangId, fn ($q) => $q->where('inventaris_barang_id', $barangId))
            ->when($tipe, fn ($q) => $q->where('tipe', $tipe))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $barangOptions = InventarisBarang::query()->orderBy('nama')->get();

        return view('inventaris.mutasi.index', compact('mutasis', 'barangOptions', 'barangId', 'tipe'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', InventarisMutasi::class);

        $barangId = $request->query('barang_id');
        $mutasi = new InventarisMutasi([
            'tanggal' => now()->toDateString(),
            'tipe' => 'in',
            'sumber_pengadaan' => 'bos',
            'jumlah' => 1,
        ]);

        $barangOptions = InventarisBarang::query()->orderBy('nama')->get();
        $tipeOptions = InventarisMutasi::TIPE_OPTIONS;

        return view('inventaris.mutasi.create', compact('mutasi', 'barangOptions', 'tipeOptions', 'barangId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventarisMutasiRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $payload['dicatat_oleh'] = Auth::id();

        InventarisMutasi::create($payload);

        return redirect()
            ->route('inventaris.mutasi.index', ['barang_id' => $payload['inventaris_barang_id']])
            ->with('success', __('Mutasi berhasil dicatat.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventarisMutasi $inventaris_mutasi): View
    {
        $this->authorize('update', $inventaris_mutasi);

        $mutasi = $inventaris_mutasi->load(['barang', 'dicatatOleh']);
        $barangOptions = InventarisBarang::query()->orderBy('nama')->get();
        $tipeOptions = InventarisMutasi::TIPE_OPTIONS;

        return view('inventaris.mutasi.edit', compact('mutasi', 'barangOptions', 'tipeOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventarisMutasiRequest $request, InventarisMutasi $inventaris_mutasi): RedirectResponse
    {
        $this->authorize('update', $inventaris_mutasi);

        $inventaris_mutasi->update($request->validated());

        return redirect()
            ->route('inventaris.mutasi.edit', $inventaris_mutasi)
            ->with('success', __('Mutasi berhasil diperbarui.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventarisMutasi $inventaris_mutasi): RedirectResponse
    {
        $this->authorize('delete', $inventaris_mutasi);

        $barangId = $inventaris_mutasi->inventaris_barang_id;
        $inventaris_mutasi->delete();

        return redirect()
            ->route('inventaris.mutasi.index', ['barang_id' => $barangId])
            ->with('success', __('Mutasi berhasil dihapus.'));
    }
}
