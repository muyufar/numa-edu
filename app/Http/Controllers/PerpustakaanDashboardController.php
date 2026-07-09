<?php

namespace App\Http\Controllers;

use App\Models\PerpustakaanBuku;
use App\Models\PerpustakaanPeminjaman;
use App\Support\PolicyRoles;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PerpustakaanDashboardController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', PerpustakaanBuku::class);

        $user = auth()->user();
        $isPetugas = PolicyRoles::perpusTim($user);

        $stats = [
            'total_buku' => PerpustakaanBuku::query()->aktif()->count(),
            'buku_fisik' => PerpustakaanBuku::query()->aktif()->whereIn('tipe', ['fisik', 'fisik_digital'])->count(),
            'buku_digital' => PerpustakaanBuku::query()->aktif()->whereIn('tipe', ['digital', 'fisik_digital'])->whereNotNull('file_path')->count(),
            'dipinjam' => PerpustakaanPeminjaman::query()->aktif()->count(),
            'terlambat' => PerpustakaanPeminjaman::query()->aktif()->whereDate('tanggal_jatuh_tempo', '<', now()->toDateString())->count(),
        ];

        $peminjamanTerbaru = PerpustakaanPeminjaman::query()
            ->with(['buku:id,judul,pengarang', 'siswa:id,nama', 'guru:id,nama', 'user:id,name'])
            ->when(! $isPetugas, fn ($q) => $q->untukUser($user))
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $bukuPopuler = PerpustakaanBuku::query()
            ->aktif()
            ->withCount(['peminjamans' => fn ($q) => $q->where('created_at', '>=', now()->subMonths(3))])
            ->orderByDesc('peminjamans_count')
            ->limit(6)
            ->get();

        return view('perpustakaan.dashboard', compact('stats', 'peminjamanTerbaru', 'bukuPopuler', 'isPetugas'));
    }
}
