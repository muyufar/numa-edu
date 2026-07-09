<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBkSanksiRequest;
use App\Http\Requests\UpdateBkSanksiRequest;
use App\Models\BkSanksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BkSanksiController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', BkSanksi::class);

        $rows = BkSanksi::query()
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->paginate(25);

        return view('bk.sanksi.index', compact('rows'));
    }

    public function create(): View
    {
        Gate::authorize('create', BkSanksi::class);

        return view('bk.sanksi.create', ['row' => new BkSanksi()]);
    }

    public function store(StoreBkSanksiRequest $request): RedirectResponse
    {
        BkSanksi::query()->create($request->validated());

        return redirect()
            ->route('bk.sanksi.index')
            ->with('status', __('Sanksi ditambahkan.'));
    }

    public function edit(BkSanksi $bk_sanksi): View
    {
        Gate::authorize('update', $bk_sanksi);

        return view('bk.sanksi.edit', ['row' => $bk_sanksi]);
    }

    public function update(UpdateBkSanksiRequest $request, BkSanksi $bk_sanksi): RedirectResponse
    {
        $bk_sanksi->update($request->validated());

        return redirect()
            ->route('bk.sanksi.index')
            ->with('status', __('Sanksi diperbarui.'));
    }

    public function destroy(BkSanksi $bk_sanksi): RedirectResponse
    {
        Gate::authorize('delete', $bk_sanksi);

        $bk_sanksi->delete();

        return redirect()
            ->route('bk.sanksi.index')
            ->with('status', __('Sanksi dihapus.'));
    }
}
