<?php

namespace App\Http\Controllers\PengurusCabang;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SekolahPilihController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Sekolah::class);

        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $sekolahs = Sekolah::withoutGlobalScopes()
                ->with('cabang:id,nama')
                ->orderByDesc('is_active')
                ->orderBy('cabang_id')
                ->orderBy('nama')
                ->get();

            return view('pengurus.sekolah-pilih', [
                'sekolahs' => $sekolahs,
                'missingCabang' => false,
                'isSuperAdmin' => true,
            ]);
        }

        abort_unless($user->hasRole('pengurus_cabang'), 403);

        if (! $user->cabang_id) {
            return view('pengurus.sekolah-pilih', [
                'sekolahs' => collect(),
                'missingCabang' => true,
                'isSuperAdmin' => false,
            ]);
        }

        $sekolahs = Sekolah::withoutGlobalScopes()
            ->where('cabang_id', $user->cabang_id)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('pengurus.sekolah-pilih', [
            'sekolahs' => $sekolahs,
            'missingCabang' => false,
            'isSuperAdmin' => false,
        ]);
    }

    public function pilih(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user?->hasRole('pengurus_cabang'), 403);
        abort_unless($user->cabang_id, 403);

        $validated = $request->validate([
            'sekolah_id' => ['required', 'integer', 'exists:sekolahs,id'],
        ]);

        $sekolah = Sekolah::withoutGlobalScopes()->whereKey($validated['sekolah_id'])->firstOrFail();
        abort_unless((int) $sekolah->cabang_id === (int) $user->cabang_id, 403);
        abort_unless($sekolah->is_active, 403);

        session(['pengurus_sekolah_id' => (int) $sekolah->id]);

        return redirect()->route('dashboard')->with('status', __('Sekolah aktif: :nama', ['nama' => $sekolah->nama]));
    }

    public function reset(): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user?->hasRole('pengurus_cabang'), 403);

        session()->forget('pengurus_sekolah_id');

        return redirect()->route('pengurus.sekolah.index')->with('status', __('Konteks sekolah dihapus.'));
    }
}
