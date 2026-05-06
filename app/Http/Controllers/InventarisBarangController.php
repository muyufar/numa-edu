<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventarisBarangRequest;
use App\Http\Requests\UpdateInventarisBarangRequest;
use App\Models\InventarisBarang;
use App\Models\InventarisKategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventarisBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', InventarisBarang::class);

        $q = trim((string) $request->query('q', ''));
        $kategoriId = $request->query('kategori_id');
        $active = $request->query('active', '1'); // 1|0|all

        $barangs = InventarisBarang::query()
            ->with('kategori')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('nama', 'like', '%'.$q.'%')
                        ->orWhere('kode', 'like', '%'.$q.'%');
                });
            })
            ->when($kategoriId, fn ($query) => $query->where('inventaris_kategori_id', $kategoriId))
            ->when($active !== 'all', fn ($query) => $query->where('is_active', $active === '1'))
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        $kategoriOptions = InventarisKategori::query()->orderBy('nama')->get();

        return view('inventaris.barang.index', compact('barangs', 'kategoriOptions', 'q', 'kategoriId', 'active'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', InventarisBarang::class);

        $barang = new InventarisBarang([
            'satuan' => 'unit',
            'stok_awal' => 0,
            'stok_minimum' => 0,
            'is_active' => true,
        ]);

        $kategoriOptions = InventarisKategori::query()->orderBy('nama')->get();

        return view('inventaris.barang.create', compact('barang', 'kategoriOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventarisBarangRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $payload['is_active'] = (bool) ($payload['is_active'] ?? false);

        $barang = InventarisBarang::create($payload);

        return redirect()
            ->route('inventaris.barang.edit', $barang)
            ->with('success', __('Barang berhasil dibuat.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventarisBarang $inventaris_barang): View
    {
        $this->authorize('update', $inventaris_barang);

        $barang = $inventaris_barang->load('kategori');
        $kategoriOptions = InventarisKategori::query()->orderBy('nama')->get();

        return view('inventaris.barang.edit', compact('barang', 'kategoriOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventarisBarangRequest $request, InventarisBarang $inventaris_barang): RedirectResponse
    {
        $this->authorize('update', $inventaris_barang);

        $payload = $request->validated();
        $payload['is_active'] = (bool) ($payload['is_active'] ?? false);

        $inventaris_barang->update($payload);

        return redirect()
            ->route('inventaris.barang.edit', $inventaris_barang)
            ->with('success', __('Barang berhasil diperbarui.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventarisBarang $inventaris_barang): RedirectResponse
    {
        $this->authorize('delete', $inventaris_barang);

        $inventaris_barang->delete();

        return redirect()
            ->route('inventaris.barang.index')
            ->with('success', __('Barang berhasil dihapus.'));
    }
}
