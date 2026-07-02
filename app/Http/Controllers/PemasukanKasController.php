<?php

namespace App\Http\Controllers;

use App\Models\AkuntansiAkun;
use App\Models\PemasukanKas;
use App\Models\Tagihan;
use App\Support\KeuanganBuktiNotaStorage;
use App\Support\KeuanganTenant;
use App\Support\PemasukanKasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PemasukanKasController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $query = PemasukanKas::query()
            ->with(['akunPendapatan:id,kode,nama', 'dibuatOleh:id,name'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($request->filled('tanggal_from')) {
            $query->whereDate('tanggal', '>=', $request->string('tanggal_from'));
        }
        if ($request->filled('tanggal_to')) {
            $query->whereDate('tanggal', '<=', $request->string('tanggal_to'));
        }

        $items = $query->paginate(20)->withQueryString();

        return view('keuangan.pemasukan-kas.index', compact('items'));
    }

    public function create(): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $akunPendapatan = AkuntansiAkun::query()
            ->where('tipe', 'pendapatan')
            ->where('is_active', true)
            ->orderBy('kode')
            ->get(['id', 'kode', 'nama']);

        return view('keuangan.pemasukan-kas.create', compact('akunPendapatan'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['required', 'string', 'max:500'],
            'no_bukti' => ['nullable', 'string', 'max:64'],
            'akun_pendapatan_id' => ['nullable', 'integer', 'exists:akuntansi_akuns,id'],
            'bukti_nota' => ['nullable', 'file', 'max:'.KeuanganBuktiNotaStorage::MAX_KB, 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        $user = $request->user();
        $sekolahId = KeuanganTenant::sekolahId($user);

        if (! empty($validated['akun_pendapatan_id'])) {
            $exists = AkuntansiAkun::query()
                ->whereKey($validated['akun_pendapatan_id'])
                ->where('sekolah_id', $sekolahId)
                ->where('tipe', 'pendapatan')
                ->where('is_active', true)
                ->exists();
            if (! $exists) {
                return back()->withErrors(['akun_pendapatan_id' => __('Akun pendapatan tidak valid.')])->withInput();
            }
        }

        $validated['bukti_nota_path'] = KeuanganBuktiNotaStorage::store(
            $request->file('bukti_nota'),
            $sekolahId,
            'pemasukan'
        );

        PemasukanKasService::create($sekolahId, (int) $user->id, $validated);

        return redirect()
            ->route('keuangan.pemasukan-kas.index')
            ->with('status', __('Pemasukan kas berhasil dicatat.'));
    }

    public function buktiNota(PemasukanKas $pemasukanKas): StreamedResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        abort_unless(
            $pemasukanKas->bukti_nota_path && Storage::disk('public')->exists($pemasukanKas->bukti_nota_path),
            404
        );

        $name = KeuanganBuktiNotaStorage::downloadName($pemasukanKas->bukti_nota_path, 'bukti-pemasukan-'.$pemasukanKas->id);

        return Storage::disk('public')->download($pemasukanKas->bukti_nota_path, $name);
    }

    public function destroy(PemasukanKas $pemasukanKas): RedirectResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        PemasukanKasService::destroyWithJurnal($pemasukanKas);

        return redirect()
            ->route('keuangan.pemasukan-kas.index')
            ->with('status', __('Pemasukan dan jurnal terkait dihapus.'));
    }
}
