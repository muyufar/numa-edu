<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranRequest;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Support\PembayaranService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PembayaranController extends Controller
{
    public function store(StorePembayaranRequest $request, Tagihan $tagihan): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($tagihan, $data, $request): void {
            PembayaranService::record($tagihan, $data, $request->user()->id);
        });

        return redirect()
            ->route('tagihan.show', $tagihan)
            ->with('status', __('Pembayaran dicatat.'));
    }

    public function destroy(Pembayaran $pembayaran): RedirectResponse
    {
        Gate::authorize('delete', $pembayaran);

        $tagihan = $pembayaran->tagihan;

        DB::transaction(function () use ($pembayaran): void {
            PembayaranService::deletePembayaranAndJurnal($pembayaran);
        });

        return redirect()
            ->route('tagihan.show', $tagihan)
            ->with('status', __('Pembayaran dihapus.'));
    }
}
