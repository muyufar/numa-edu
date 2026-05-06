<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Support\PeriodeBulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KeuanganRekapController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $request->merge([
            'kelas_id' => $request->filled('kelas_id') ? $request->input('kelas_id') : null,
        ]);

        $data = $request->validate([
            'periode_from' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'periode_to' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
        ]);

        $periodeFrom = $data['periode_from'] ?? now()->format('Y-m');
        $periodeTo = $data['periode_to'] ?? $periodeFrom;
        [$periodeFrom, $periodeTo] = PeriodeBulan::orderMonths($periodeFrom, $periodeTo);
        $kelasId = $data['kelas_id'] ?? null;

        $tagihanQ = Tagihan::query()
            ->withSum('pembayarans as total_bayar', 'jumlah')
            ->whereBetween('periode', [$periodeFrom, $periodeTo])
            ->when($kelasId, fn ($q) => $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId)));

        $all = $tagihanQ->get(['id', 'siswa_id', 'jenis', 'periode', 'jumlah', 'status']);

        $totalTagihan = (float) $all->sum(fn ($t) => (float) $t->jumlah);
        $totalDibayar = (float) $all->sum(fn ($t) => (float) ($t->total_bayar ?? 0));
        $totalSisa = (float) $all->sum(fn ($t) => max(0, (float) $t->jumlah - (float) ($t->total_bayar ?? 0)));

        $byStatus = [
            'unpaid' => (int) $all->where('status', 'unpaid')->count(),
            'partial' => (int) $all->where('status', 'partial')->count(),
            'paid' => (int) $all->where('status', 'paid')->count(),
        ];

        // Piutang per siswa (top 50)
        $piutangSiswa = Tagihan::query()
            ->with(['siswa:id,nis,nama,kelas_id', 'siswa.kelas:id,tingkat,nama,tahun_ajaran'])
            ->withSum('pembayarans as total_bayar', 'jumlah')
            ->whereBetween('periode', [$periodeFrom, $periodeTo])
            ->when($kelasId, fn ($q) => $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId)))
            ->get()
            ->groupBy('siswa_id')
            ->map(function ($rows) {
                $first = $rows->first();
                $total = (float) $rows->sum(fn ($t) => (float) $t->jumlah);
                $bayar = (float) $rows->sum(fn ($t) => (float) ($t->total_bayar ?? 0));
                $sisa = max(0, $total - $bayar);

                return [
                    'siswa' => $first?->siswa,
                    'total' => $total,
                    'dibayar' => $bayar,
                    'sisa' => $sisa,
                ];
            })
            ->sortByDesc('sisa')
            ->take(50)
            ->values();

        // Piutang per kelas
        $piutangKelas = Tagihan::query()
            ->with(['siswa.kelas:id,tingkat,nama,tahun_ajaran'])
            ->withSum('pembayarans as total_bayar', 'jumlah')
            ->whereBetween('periode', [$periodeFrom, $periodeTo])
            ->get()
            ->groupBy(fn ($t) => $t->siswa?->kelas_id ?: 0)
            ->map(function ($rows, $kelasId) {
                $first = $rows->first();
                $kelas = $first?->siswa?->kelas;
                $total = (float) $rows->sum(fn ($t) => (float) $t->jumlah);
                $bayar = (float) $rows->sum(fn ($t) => (float) ($t->total_bayar ?? 0));
                $sisa = max(0, $total - $bayar);

                return [
                    'kelas_id' => (int) $kelasId,
                    'kelas' => $kelas,
                    'total' => $total,
                    'dibayar' => $bayar,
                    'sisa' => $sisa,
                ];
            })
            ->sortByDesc('sisa')
            ->values();

        $kelasOptions = Kelas::query()
            ->where('is_active', true)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran']);

        // Total pemasukan berdasarkan pembayaran (untuk cross-check)
        $pemasukan = (float) Pembayaran::query()
            ->whereHas('tagihan', function ($q) use ($periodeFrom, $periodeTo, $kelasId) {
                $q->whereBetween('periode', [$periodeFrom, $periodeTo]);
                if ($kelasId) {
                    $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId));
                }
            })
            ->sum('jumlah');

        return view('keuangan.rekap.index', compact(
            'periodeFrom',
            'periodeTo',
            'kelasId',
            'kelasOptions',
            'totalTagihan',
            'totalDibayar',
            'totalSisa',
            'byStatus',
            'piutangSiswa',
            'piutangKelas',
            'pemasukan',
        ));
    }

    public function showSiswa(Request $request, Siswa $siswa): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $data = $request->validate([
            'periode_from' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'periode_to' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
        ]);

        $periodeFrom = $data['periode_from'] ?? now()->format('Y-m');
        $periodeTo = $data['periode_to'] ?? $periodeFrom;
        [$periodeFrom, $periodeTo] = PeriodeBulan::orderMonths($periodeFrom, $periodeTo);

        $tagihans = Tagihan::query()
            ->where('siswa_id', $siswa->id)
            ->whereBetween('periode', [$periodeFrom, $periodeTo])
            ->withSum('pembayarans as total_bayar', 'jumlah')
            ->orderBy('periode')
            ->orderBy('jenis')
            ->get();

        $rows = $tagihans
            ->map(function (Tagihan $t): ?array {
                $dibayar = (float) ($t->total_bayar ?? 0);
                $sisa = max(0, (float) $t->jumlah - $dibayar);

                return $sisa > 0.00001 ? ['tagihan' => $t, 'dibayar' => $dibayar, 'sisa' => $sisa] : null;
            })
            ->filter()
            ->values();

        $totalTagihan = (float) $tagihans->sum(fn ($t) => (float) $t->jumlah);
        $totalDibayar = (float) $tagihans->sum(fn ($t) => (float) ($t->total_bayar ?? 0));
        $totalSisa = (float) $rows->sum('sisa');

        $siswa->load('kelas:id,tingkat,nama,tahun_ajaran');

        return view('keuangan.rekap.siswa', compact(
            'siswa',
            'periodeFrom',
            'periodeTo',
            'rows',
            'totalTagihan',
            'totalDibayar',
            'totalSisa',
        ));
    }

    public function showKelas(Request $request, Kelas $kelas): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $data = $request->validate([
            'periode_from' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'periode_to' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
        ]);

        $periodeFrom = $data['periode_from'] ?? now()->format('Y-m');
        $periodeTo = $data['periode_to'] ?? $periodeFrom;
        [$periodeFrom, $periodeTo] = PeriodeBulan::orderMonths($periodeFrom, $periodeTo);

        $kelas->load('siswas:id,nis,nama,kelas_id');

        $bySiswa = Tagihan::query()
            ->with(['siswa:id,nis,nama,kelas_id'])
            ->withSum('pembayarans as total_bayar', 'jumlah')
            ->whereBetween('periode', [$periodeFrom, $periodeTo])
            ->whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelas->id))
            ->get()
            ->groupBy('siswa_id')
            ->map(function ($rows) {
                $first = $rows->first();
                $total = (float) $rows->sum(fn ($t) => (float) $t->jumlah);
                $bayar = (float) $rows->sum(fn ($t) => (float) ($t->total_bayar ?? 0));
                $sisa = max(0, $total - $bayar);

                return [
                    'siswa' => $first?->siswa,
                    'total' => $total,
                    'dibayar' => $bayar,
                    'sisa' => $sisa,
                ];
            })
            ->sortByDesc('sisa')
            ->values();

        $totalSisa = (float) $bySiswa->sum('sisa');

        return view('keuangan.rekap.kelas', compact(
            'kelas',
            'periodeFrom',
            'periodeTo',
            'bySiswa',
            'totalSisa',
        ));
    }
}

