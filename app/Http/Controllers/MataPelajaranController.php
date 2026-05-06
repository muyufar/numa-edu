<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMataPelajaranRequest;
use App\Http\Requests\UpdateMataPelajaranRequest;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MataPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', MataPelajaran::class);

        $mapel = MataPelajaran::query()
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        return view('mapel.index', compact('mapel'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', MataPelajaran::class);

        return view('mapel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMataPelajaranRequest $request): RedirectResponse
    {
        MataPelajaran::query()->create($request->validated());

        return redirect()
            ->route('mapel.index')
            ->with('status', __('Mata pelajaran berhasil ditambahkan.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MataPelajaran $mataPelajaran): RedirectResponse
    {
        return redirect()->route('mapel.edit', $mataPelajaran);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MataPelajaran $mataPelajaran): View
    {
        Gate::authorize('update', $mataPelajaran);

        return view('mapel.edit', ['mapel' => $mataPelajaran]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMataPelajaranRequest $request, MataPelajaran $mataPelajaran): RedirectResponse
    {
        $mataPelajaran->update($request->validated());

        return redirect()
            ->route('mapel.index')
            ->with('status', __('Mata pelajaran berhasil diperbarui.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MataPelajaran $mataPelajaran): RedirectResponse
    {
        Gate::authorize('delete', $mataPelajaran);

        $mataPelajaran->delete();

        return redirect()
            ->route('mapel.index')
            ->with('status', __('Mata pelajaran berhasil dihapus.'));
    }
}
