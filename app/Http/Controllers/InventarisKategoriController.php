<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventarisKategoriRequest;
use App\Http\Requests\UpdateInventarisKategoriRequest;
use App\Models\InventarisKategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventarisKategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', InventarisKategori::class);

        $kategoris = InventarisKategori::query()
            ->orderBy('nama')
            ->paginate(25);

        return view('inventaris.kategori.index', compact('kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', InventarisKategori::class);

        $kategori = new InventarisKategori();

        return view('inventaris.kategori.create', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventarisKategoriRequest $request): RedirectResponse
    {
        $kategori = InventarisKategori::create($request->validated());

        return redirect()
            ->route('inventaris.kategori.edit', $kategori)
            ->with('success', __('Kategori berhasil dibuat.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventarisKategori $inventaris_kategori): View
    {
        $this->authorize('update', $inventaris_kategori);

        $kategori = $inventaris_kategori;

        return view('inventaris.kategori.edit', compact('kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventarisKategoriRequest $request, InventarisKategori $inventaris_kategori): RedirectResponse
    {
        $this->authorize('update', $inventaris_kategori);

        $inventaris_kategori->update($request->validated());

        return redirect()
            ->route('inventaris.kategori.edit', $inventaris_kategori)
            ->with('success', __('Kategori berhasil diperbarui.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventarisKategori $inventaris_kategori): RedirectResponse
    {
        $this->authorize('delete', $inventaris_kategori);

        $inventaris_kategori->delete();

        return redirect()
            ->route('inventaris.kategori.index')
            ->with('success', __('Kategori berhasil dihapus.'));
    }
}
