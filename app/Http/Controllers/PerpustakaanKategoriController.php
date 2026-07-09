<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerpustakaanKategoriRequest;
use App\Http\Requests\UpdatePerpustakaanKategoriRequest;
use App\Models\PerpustakaanKategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerpustakaanKategoriController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', PerpustakaanKategori::class);

        $q = trim((string) $request->query('q', ''));

        $kategoris = PerpustakaanKategori::query()
            ->withCount('bukus')
            ->when($q !== '', fn ($query) => $query->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"))
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        return view('perpustakaan.kategori.index', compact('kategoris', 'q'));
    }

    public function create(): View
    {
        $this->authorize('create', PerpustakaanKategori::class);

        return view('perpustakaan.kategori.create', ['kategori' => new PerpustakaanKategori]);
    }

    public function store(StorePerpustakaanKategoriRequest $request): RedirectResponse
    {
        $kategori = PerpustakaanKategori::query()->create($request->validated());

        return redirect()
            ->route('perpustakaan.kategori.index')
            ->with('status', __('Kategori berhasil ditambahkan.'));
    }

    public function edit(PerpustakaanKategori $perpustakaan_kategori): View
    {
        $this->authorize('update', $perpustakaan_kategori);

        return view('perpustakaan.kategori.edit', ['kategori' => $perpustakaan_kategori]);
    }

    public function update(UpdatePerpustakaanKategoriRequest $request, PerpustakaanKategori $perpustakaan_kategori): RedirectResponse
    {
        $perpustakaan_kategori->update($request->validated());

        return redirect()
            ->route('perpustakaan.kategori.index')
            ->with('status', __('Kategori berhasil diperbarui.'));
    }

    public function destroy(PerpustakaanKategori $perpustakaan_kategori): RedirectResponse
    {
        $this->authorize('delete', $perpustakaan_kategori);

        if ($perpustakaan_kategori->bukus()->exists()) {
            return back()->withErrors(['kategori' => __('Kategori masih digunakan oleh buku.')]);
        }

        $perpustakaan_kategori->delete();

        return redirect()
            ->route('perpustakaan.kategori.index')
            ->with('status', __('Kategori dihapus.'));
    }
}
