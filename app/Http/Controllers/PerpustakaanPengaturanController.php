<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePerpustakaanPengaturanRequest;
use App\Models\PerpustakaanPengaturan;
use App\Support\PerpustakaanTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PerpustakaanPengaturanController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        Gate::authorize('viewAny', PerpustakaanPengaturan::class);

        if (PerpustakaanTenant::pengurusCabangNeedsPilihSekolah()) {
            return redirect()
                ->route('pengurus.sekolah.index')
                ->with('status', __('Pilih sekolah terlebih dahulu untuk mengatur perpustakaan.'));
        }

        $pengaturan = PerpustakaanPengaturan::forSekolah(PerpustakaanTenant::sekolahId());

        return view('perpustakaan.pengaturan.edit', compact('pengaturan'));
    }

    public function update(UpdatePerpustakaanPengaturanRequest $request): RedirectResponse
    {
        if (PerpustakaanTenant::pengurusCabangNeedsPilihSekolah()) {
            return redirect()
                ->route('pengurus.sekolah.index')
                ->with('status', __('Pilih sekolah terlebih dahulu untuk mengatur perpustakaan.'));
        }

        $pengaturan = PerpustakaanPengaturan::forSekolah(PerpustakaanTenant::sekolahId());
        Gate::authorize('update', $pengaturan);

        $pengaturan->update($request->validated());

        return redirect()
            ->route('perpustakaan.pengaturan.edit')
            ->with('status', __('Pengaturan perpustakaan disimpan.'));
    }
}
