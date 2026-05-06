<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KeuanganHubController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $stats = [
            'tagihan_unpaid' => (int) Tagihan::query()->where('status', 'unpaid')->count(),
            'tagihan_partial' => (int) Tagihan::query()->where('status', 'partial')->count(),
            'tagihan_paid' => (int) Tagihan::query()->where('status', 'paid')->count(),
        ];

        $outstanding = (float) (Tagihan::query()
            ->whereIn('status', ['unpaid', 'partial'])
            ->selectRaw(
                'COALESCE(SUM(tagihans.jumlah - COALESCE((SELECT SUM(jumlah) FROM pembayarans WHERE pembayarans.tagihan_id = tagihans.id), 0)), 0) as total'
            )
            ->value('total') ?? 0);

        $pemasukanBulanIni = (float) Pembayaran::query()
            ->whereYear('dibayar_pada', (int) now()->year)
            ->whereMonth('dibayar_pada', (int) now()->month)
            ->sum('jumlah');

        $recentTagihan = Tagihan::query()
            ->with(['siswa:id,nis,nama'])
            ->withSum('pembayarans as total_bayar', 'jumlah')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        return view('keuangan.index', compact('stats', 'outstanding', 'pemasukanBulanIni', 'recentTagihan'));
    }
}
