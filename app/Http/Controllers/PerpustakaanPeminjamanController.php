<?php

namespace App\Http\Controllers;

use App\Models\PerpustakaanPeminjaman;
use App\Services\PerpustakaanPeminjamanService;
use App\Support\PolicyRoles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PerpustakaanPeminjamanController extends Controller
{
    public function __construct(
        private readonly PerpustakaanPeminjamanService $peminjamanService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', PerpustakaanPeminjaman::class);

        $user = auth()->user();
        $isPetugas = PolicyRoles::perpusTim($user);
        $tab = (string) $request->query('tab', $isPetugas ? 'semua' : 'saya');

        $query = PerpustakaanPeminjaman::query()
            ->with(['buku:id,judul,pengarang,tipe', 'siswa:id,nama', 'guru:id,nama', 'user:id,name'])
            ->orderByDesc('id');

        if ($tab === 'saya' || ! $isPetugas) {
            $query->untukUser($user);
        } elseif ($tab === 'aktif') {
            $query->aktif();
        } elseif ($tab === 'terlambat') {
            $query->aktif()->whereDate('tanggal_jatuh_tempo', '<', now()->toDateString());
        }

        if ($q = trim((string) $request->query('q'))) {
            $query->whereHas('buku', fn ($b) => $b->where('judul', 'like', "%{$q}%"));
        }

        $peminjamans = $query->paginate(20)->withQueryString();

        return view('perpustakaan.peminjaman.index', compact('peminjamans', 'tab', 'isPetugas', 'q'));
    }

    public function show(PerpustakaanPeminjaman $perpustakaan_peminjaman): View
    {
        Gate::authorize('view', $perpustakaan_peminjaman);

        $peminjaman = $perpustakaan_peminjaman->load(['buku.kategori', 'siswa', 'guru', 'user', 'diprosesOleh']);

        return view('perpustakaan.peminjaman.show', compact('peminjaman'));
    }

    public function kembalikan(Request $request, PerpustakaanPeminjaman $perpustakaan_peminjaman): RedirectResponse
    {
        Gate::authorize('kembalikan', $perpustakaan_peminjaman);

        $this->peminjamanService->kembalikan(
            $perpustakaan_peminjaman,
            auth()->user(),
            $request->input('catatan'),
        );

        return redirect()
            ->route('perpustakaan.peminjaman.show', $perpustakaan_peminjaman)
            ->with('status', __('Buku berhasil dikembalikan.'));
    }

    public function perpanjang(PerpustakaanPeminjaman $perpustakaan_peminjaman): RedirectResponse
    {
        Gate::authorize('perpanjang', $perpustakaan_peminjaman);

        $this->peminjamanService->perpanjang($perpustakaan_peminjaman, auth()->user());

        return redirect()
            ->route('perpustakaan.peminjaman.show', $perpustakaan_peminjaman)
            ->with('status', __('Peminjaman diperpanjang.'));
    }

    public function tandaiHilang(Request $request, PerpustakaanPeminjaman $perpustakaan_peminjaman): RedirectResponse
    {
        Gate::authorize('tandaiHilang', $perpustakaan_peminjaman);

        $this->peminjamanService->tandaiHilang(
            $perpustakaan_peminjaman,
            auth()->user(),
            $request->input('catatan'),
        );

        return redirect()
            ->route('perpustakaan.peminjaman.show', $perpustakaan_peminjaman)
            ->with('status', __('Peminjaman ditandai hilang.'));
    }
}
