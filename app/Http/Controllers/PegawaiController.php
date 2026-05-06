<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\Models\Pegawai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Pegawai::class);

        $pegawais = Pegawai::query()
            ->orderByDesc('is_active')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('pegawai.index', compact('pegawais'));
    }

    public function create(): View
    {
        Gate::authorize('create', Pegawai::class);

        return view('pegawai.create');
    }

    public function store(StorePegawaiRequest $request): RedirectResponse
    {
        Pegawai::query()->create($request->validated());

        return redirect()
            ->route('pegawai.index')
            ->with('status', __('Pegawai ditambahkan.'));
    }

    public function edit(Pegawai $pegawai): View
    {
        Gate::authorize('update', $pegawai);

        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai): RedirectResponse
    {
        $pegawai->update($request->validated());

        return redirect()
            ->route('pegawai.index')
            ->with('status', __('Data pegawai diperbarui.'));
    }

    public function destroy(Pegawai $pegawai): RedirectResponse
    {
        Gate::authorize('delete', $pegawai);

        $pegawai->delete();

        return redirect()
            ->route('pegawai.index')
            ->with('status', __('Pegawai dihapus.'));
    }
}
