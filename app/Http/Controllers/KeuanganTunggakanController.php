<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Tagihan;
use App\Support\DateTimeFormat;
use App\Support\PeriodeBulan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KeuanganTunggakanController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $request->merge([
            'kelas_id' => $request->filled('kelas_id') ? $request->input('kelas_id') : null,
            'min_sisa' => $request->filled('min_sisa') ? $request->input('min_sisa') : null,
        ]);

        $filters = $request->validate([
            'periode_from' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'periode_to' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'min_sisa' => ['nullable', 'numeric', 'min:0'],
        ]);

        $periodeFrom = $filters['periode_from'] ?? now()->subMonths(11)->format('Y-m');
        $periodeTo = $filters['periode_to'] ?? now()->addMonth()->format('Y-m');
        [$periodeFrom, $periodeTo] = PeriodeBulan::orderMonths($periodeFrom, $periodeTo);
        $kelasId = $filters['kelas_id'] ?? null;
        $minSisa = isset($filters['min_sisa']) && $filters['min_sisa'] !== null && $filters['min_sisa'] !== ''
            ? (float) $filters['min_sisa']
            : null;

        $summaryRows = $this->tunggakanBaseQuery($periodeFrom, $periodeTo, $kelasId, $minSisa)
            ->get(['id', 'jumlah', 'total_bayar']);
        $jumlahTunggakan = $summaryRows->count();
        $totalSisa = (float) $summaryRows->sum(fn ($t) => max(0, (float) $t->jumlah - (float) ($t->total_bayar ?? 0)));

        $rows = $this->tunggakanBaseQuery($periodeFrom, $periodeTo, $kelasId, $minSisa)
            ->with(['siswa:id,nama,nis,kelas_id', 'siswa.kelas:id,tingkat,nama,tahun_ajaran'])
            ->orderByDesc('periode')
            ->orderByDesc('jumlah')
            ->paginate(30)
            ->withQueryString();

        $kelasOptions = Kelas::query()
            ->where('is_active', true)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran']);

        return view('keuangan.tunggakan.index', compact(
            'rows',
            'kelasOptions',
            'periodeFrom',
            'periodeTo',
            'kelasId',
            'minSisa',
            'jumlahTunggakan',
            'totalSisa',
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        $request->merge([
            'kelas_id' => $request->filled('kelas_id') ? $request->input('kelas_id') : null,
            'min_sisa' => $request->filled('min_sisa') ? $request->input('min_sisa') : null,
        ]);

        $filters = $request->validate([
            'periode_from' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'periode_to' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'min_sisa' => ['nullable', 'numeric', 'min:0'],
        ]);

        $periodeFrom = $filters['periode_from'] ?? now()->subMonths(11)->format('Y-m');
        $periodeTo = $filters['periode_to'] ?? now()->addMonth()->format('Y-m');
        [$periodeFrom, $periodeTo] = PeriodeBulan::orderMonths($periodeFrom, $periodeTo);
        $kelasId = $filters['kelas_id'] ?? null;
        $minSisa = isset($filters['min_sisa']) && $filters['min_sisa'] !== null && $filters['min_sisa'] !== ''
            ? (float) $filters['min_sisa']
            : null;

        $filename = 'tunggakan-'.$periodeFrom.'-'.$periodeTo.'-'.now()->format('His').'.csv';

        return response()->streamDownload(function () use ($periodeFrom, $periodeTo, $kelasId, $minSisa): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['nis', 'nama_siswa', 'kelas', 'jenis', 'periode', 'jumlah', 'total_dibayar', 'sisa', 'status', 'jatuh_tempo']);

            $this->tunggakanBaseQuery($periodeFrom, $periodeTo, $kelasId, $minSisa)
                ->with(['siswa.kelas:id,tingkat,nama,tahun_ajaran'])
                ->orderBy('periode')
                ->orderBy('siswa_id')
                ->chunk(200, function ($chunk) use ($out): void {
                    foreach ($chunk as $t) {
                        $s = $t->siswa;
                        $kelas = $s?->kelas;
                        $kelasLabel = $kelas ? trim("{$kelas->tingkat} {$kelas->nama} {$kelas->tahun_ajaran}") : '';
                        $bayar = (float) ($t->total_bayar ?? 0);
                        $sisa = max(0, (float) $t->jumlah - $bayar);
                        fputcsv($out, [
                            $s?->nis ?? '',
                            $s?->nama ?? '',
                            $kelasLabel,
                            $t->jenis,
                            $t->periode,
                            number_format((float) $t->jumlah, 2, '.', ''),
                            number_format($bayar, 2, '.', ''),
                            number_format($sisa, 2, '.', ''),
                            $t->status,
                            $t->jatuh_tempo ? DateTimeFormat::date($t->jatuh_tempo) : '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function tunggakanBaseQuery(string $periodeFrom, string $periodeTo, ?int $kelasId, ?float $minSisa): Builder
    {
        return Tagihan::query()
            ->withSum('pembayarans as total_bayar', 'jumlah')
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereBetween('periode', [$periodeFrom, $periodeTo])
            ->when($kelasId, fn ($q) => $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId)))
            ->when($minSisa !== null && $minSisa > 0, function ($q) use ($minSisa): void {
                $q->whereRaw(
                    '(tagihans.jumlah - COALESCE((SELECT SUM(jumlah) FROM pembayarans WHERE pembayarans.tagihan_id = tagihans.id), 0)) >= ?',
                    [$minSisa]
                );
            });
    }
}
