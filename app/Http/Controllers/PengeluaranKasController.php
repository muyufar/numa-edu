<?php

namespace App\Http\Controllers;

use App\Models\AkuntansiAkun;
use App\Models\PengeluaranKas;
use App\Models\Tagihan;
use App\Support\KeuanganTenant;
use App\Support\PengeluaranKasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PengeluaranKasController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $query = PengeluaranKas::query()
            ->with(['akunBeban:id,kode,nama', 'dibuatOleh:id,name'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($request->filled('tanggal_from')) {
            $query->whereDate('tanggal', '>=', $request->string('tanggal_from'));
        }
        if ($request->filled('tanggal_to')) {
            $query->whereDate('tanggal', '<=', $request->string('tanggal_to'));
        }

        $items = $query->paginate(20)->withQueryString();

        return view('keuangan.pengeluaran-kas.index', compact('items'));
    }

    public function create(): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $akunBeban = AkuntansiAkun::query()
            ->where('tipe', 'beban')
            ->where('is_active', true)
            ->orderBy('kode')
            ->get(['id', 'kode', 'nama']);

        return view('keuangan.pengeluaran-kas.create', compact('akunBeban'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['required', 'string', 'max:500'],
            'no_bukti' => ['nullable', 'string', 'max:64'],
            'akun_beban_id' => ['nullable', 'integer', 'exists:akuntansi_akuns,id'],
        ]);

        $user = $request->user();
        $sekolahId = KeuanganTenant::sekolahId($user);

        if (! empty($validated['akun_beban_id'])) {
            $exists = AkuntansiAkun::query()
                ->whereKey($validated['akun_beban_id'])
                ->where('sekolah_id', $sekolahId)
                ->where('tipe', 'beban')
                ->where('is_active', true)
                ->exists();
            if (! $exists) {
                return back()->withErrors(['akun_beban_id' => __('Akun beban tidak valid.')])->withInput();
            }
        }

        PengeluaranKasService::create($sekolahId, (int) $user->id, $validated);

        return redirect()
            ->route('keuangan.pengeluaran-kas.index')
            ->with('status', __('Pengeluaran kas berhasil dicatat.'));
    }

    public function destroy(PengeluaranKas $pengeluaranKas): RedirectResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        PengeluaranKasService::destroyWithJurnal($pengeluaranKas);

        return redirect()
            ->route('keuangan.pengeluaran-kas.index')
            ->with('status', __('Pengeluaran dan jurnal terkait dihapus.'));
    }
}
