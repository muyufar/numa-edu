<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBeritaRequest;
use App\Http\Requests\UpdateBeritaRequest;
use App\Models\Berita;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BeritaController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Berita::class);

        $beritas = Berita::query()
            ->with('author:id,name')
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('berita.index', compact('beritas'));
    }

    public function create(): View
    {
        Gate::authorize('create', Berita::class);

        return view('berita.create');
    }

    public function store(StoreBeritaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $berita = Berita::query()->create($data);

        return redirect()
            ->route('berita.edit', $berita)
            ->with('status', __('Berita disimpan.'));
    }

    public function edit(Berita $beritum): View
    {
        Gate::authorize('update', $beritum);

        return view('berita.edit', ['berita' => $beritum]);
    }

    public function update(UpdateBeritaRequest $request, Berita $beritum): RedirectResponse
    {
        $beritum->update($request->validated());

        return redirect()
            ->route('berita.edit', $beritum)
            ->with('status', __('Berita diperbarui.'));
    }

    public function destroy(Berita $beritum): RedirectResponse
    {
        Gate::authorize('delete', $beritum);

        $beritum->delete();

        return redirect()
            ->route('berita.index')
            ->with('status', __('Berita dihapus.'));
    }
}
