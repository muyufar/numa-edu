<?php

namespace App\Http\Controllers;

use App\Models\AkuntansiJurnalLine;
use App\Models\Tagihan;
use App\Support\AkuntansiDefaults;
use App\Support\DateTimeFormat;
use App\Support\KeuanganTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BukuKasController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $sekolahId = $this->resolveSekolahId($request);
        $kas = AkuntansiDefaults::ensureForSekolah($sekolahId)['kas'];

        [$tanggalFrom, $tanggalTo] = $this->tanggalRange($request);

        $saldoAwal = (float) AkuntansiJurnalLine::query()
            ->join('akuntansi_jurnals', 'akuntansi_jurnal_lines.jurnal_id', '=', 'akuntansi_jurnals.id')
            ->where('akuntansi_jurnal_lines.sekolah_id', $sekolahId)
            ->where('akuntansi_jurnal_lines.akun_id', $kas->id)
            ->where('akuntansi_jurnals.tanggal', '<', $tanggalFrom)
            ->selectRaw('COALESCE(SUM(akuntansi_jurnal_lines.debit - akuntansi_jurnal_lines.kredit), 0) as net')
            ->value('net');

        $lines = AkuntansiJurnalLine::query()
            ->select('akuntansi_jurnal_lines.*')
            ->join('akuntansi_jurnals', 'akuntansi_jurnal_lines.jurnal_id', '=', 'akuntansi_jurnals.id')
            ->where('akuntansi_jurnal_lines.sekolah_id', $sekolahId)
            ->where('akuntansi_jurnal_lines.akun_id', $kas->id)
            ->whereBetween('akuntansi_jurnals.tanggal', [$tanggalFrom, $tanggalTo])
            ->with(['jurnal' => fn ($q) => $q->select('id', 'tanggal', 'no_bukti', 'keterangan')])
            ->orderBy('akuntansi_jurnals.tanggal')
            ->orderBy('akuntansi_jurnals.id')
            ->orderBy('akuntansi_jurnal_lines.id')
            ->get();

        $saldo = $saldoAwal;
        foreach ($lines as $line) {
            $saldo += (float) $line->debit - (float) $line->kredit;
            $line->setAttribute('saldo_setelah', $saldo);
        }

        return view('keuangan.buku-kas.index', [
            'tanggalFrom' => $tanggalFrom,
            'tanggalTo' => $tanggalTo,
            'kas' => $kas,
            'saldoAwal' => $saldoAwal,
            'lines' => $lines,
            'saldoAkhir' => $saldo,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        $sekolahId = $this->resolveSekolahId($request);
        $kas = AkuntansiDefaults::ensureForSekolah($sekolahId)['kas'];

        [$tanggalFrom, $tanggalTo] = $this->tanggalRange($request);

        $saldoAwal = (float) AkuntansiJurnalLine::query()
            ->join('akuntansi_jurnals', 'akuntansi_jurnal_lines.jurnal_id', '=', 'akuntansi_jurnals.id')
            ->where('akuntansi_jurnal_lines.sekolah_id', $sekolahId)
            ->where('akuntansi_jurnal_lines.akun_id', $kas->id)
            ->where('akuntansi_jurnals.tanggal', '<', $tanggalFrom)
            ->selectRaw('COALESCE(SUM(akuntansi_jurnal_lines.debit - akuntansi_jurnal_lines.kredit), 0) as net')
            ->value('net');

        $lines = AkuntansiJurnalLine::query()
            ->select('akuntansi_jurnal_lines.*')
            ->join('akuntansi_jurnals', 'akuntansi_jurnal_lines.jurnal_id', '=', 'akuntansi_jurnals.id')
            ->where('akuntansi_jurnal_lines.sekolah_id', $sekolahId)
            ->where('akuntansi_jurnal_lines.akun_id', $kas->id)
            ->whereBetween('akuntansi_jurnals.tanggal', [$tanggalFrom, $tanggalTo])
            ->with(['jurnal' => fn ($q) => $q->select('id', 'tanggal', 'no_bukti', 'keterangan')])
            ->orderBy('akuntansi_jurnals.tanggal')
            ->orderBy('akuntansi_jurnals.id')
            ->orderBy('akuntansi_jurnal_lines.id')
            ->get();

        $filename = 'buku-kas-'.$tanggalFrom.'-'.$tanggalTo.'-'.now()->format('His').'.csv';

        return response()->streamDownload(function () use ($lines, $saldoAwal, $tanggalFrom, $tanggalTo, $kas): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['tanggal_dari', 'tanggal_sampai', 'akun_kas', 'saldo_awal_periode']);
            fputcsv($out, [$tanggalFrom, $tanggalTo, $kas->kode.' '.$kas->nama, number_format($saldoAwal, 2, '.', '')]);
            fputcsv($out, []);
            fputcsv($out, ['tanggal', 'no_bukti', 'keterangan', 'debit', 'kredit', 'saldo']);

            $saldo = $saldoAwal;
            foreach ($lines as $line) {
                $j = $line->jurnal;
                $saldo += (float) $line->debit - (float) $line->kredit;
                fputcsv($out, [
                    $j ? DateTimeFormat::date($j->tanggal) : '',
                    $j?->no_bukti ?? '',
                    $j?->keterangan ?? '',
                    number_format((float) $line->debit, 2, '.', ''),
                    number_format((float) $line->kredit, 2, '.', ''),
                    number_format($saldo, 2, '.', ''),
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function resolveSekolahId(Request $request): int
    {
        return KeuanganTenant::sekolahId($request->user());
    }

    /**
     * @return array{0: string, 1: string} Tanggal awal dan akhir (Y-m-d), awal tidak boleh setelah akhir.
     */
    private function tanggalRange(Request $request): array
    {
        $from = $request->filled('tanggal_from')
            ? Carbon::parse($request->string('tanggal_from'))->toDateString()
            : now()->startOfMonth()->toDateString();
        $to = $request->filled('tanggal_to')
            ? Carbon::parse($request->string('tanggal_to'))->toDateString()
            : now()->endOfMonth()->toDateString();

        if (strcmp($from, $to) > 0) {
            return [$to, $from];
        }

        return [$from, $to];
    }
}
