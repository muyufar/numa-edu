<?php

namespace App\Http\Controllers;

use App\Models\AkuntansiAkun;
use App\Models\AkuntansiJurnal;
use App\Models\Tagihan;
use App\Models\User;
use App\Support\DateTimeFormat;
use App\Support\KeuanganTenant;
use App\Support\ManualJurnalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AkuntansiJurnalController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        [$tanggalFrom, $tanggalTo] = $this->tanggalRange($request);
        $q = trim((string) $request->query('q', ''));

        $rows = AkuntansiJurnal::query()
            ->withCount('lines')
            ->withSum('lines', 'debit')
            ->when($q !== '', fn ($b) => $b->where('keterangan', 'like', '%'.$q.'%'))
            ->whereBetween('tanggal', [$tanggalFrom, $tanggalTo])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('akuntansi.jurnal.index', compact('rows', 'tanggalFrom', 'tanggalTo', 'q'));
    }

    public function create(): View
    {
        Gate::authorize('create', Tagihan::class);

        $akuns = AkuntansiAkun::query()
            ->where('is_active', true)
            ->orderBy('kode')
            ->get(['id', 'kode', 'nama', 'tipe']);

        return view('akuntansi.jurnal.create', compact('akuns'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'no_bukti' => ['nullable', 'string', 'max:64'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'lines' => ['required', 'array'],
            'lines.*.akun_id' => ['nullable'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.kredit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $sekolahId = $this->resolveSekolahId($request->user());
        [$ok, $message, $lines] = ManualJurnalService::normalizeLines($sekolahId, $request->input('lines', []));

        if (! $ok) {
            return back()->withErrors(['lines' => $message])->withInput();
        }

        $tanggal = Carbon::parse($validated['tanggal'])->toDateString();

        $jurnal = ManualJurnalService::create(
            $sekolahId,
            (int) $request->user()->id,
            $tanggal,
            $validated['no_bukti'] ?? null,
            $validated['keterangan'] ?? null,
            $lines,
        );

        return redirect()
            ->route('akuntansi.jurnal.show', $jurnal)
            ->with('status', __('Jurnal manual berhasil disimpan.'));
    }

    public function show(Request $request, AkuntansiJurnal $jurnal): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $user = $request->user();
        if (! $user?->hasRole('super_admin')) {
            $sid = KeuanganTenant::sekolahId($user);
            if ((int) $jurnal->sekolah_id !== $sid) {
                abort(404);
            }
        }

        $jurnal->load(['lines.akun', 'dibuatOleh:id,name']);

        $totalDebit = (float) $jurnal->lines->sum('debit');
        $totalKredit = (float) $jurnal->lines->sum('kredit');

        return view('akuntansi.jurnal.show', compact('jurnal', 'totalDebit', 'totalKredit'));
    }

    public function destroy(Request $request, AkuntansiJurnal $jurnal): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $user = $request->user();
        if (! $user?->hasRole('super_admin')) {
            $sid = KeuanganTenant::sekolahId($user);
            if ((int) $jurnal->sekolah_id !== $sid) {
                abort(404);
            }
        }

        if (! ManualJurnalService::isManual($jurnal)) {
            return redirect()
                ->route('akuntansi.jurnal.index')
                ->withErrors(['jurnal' => __('Hanya jurnal manual yang dapat dihapus dari sini.')]);
        }

        $jurnal->delete();

        return redirect()
            ->route('akuntansi.jurnal.index')
            ->with('status', __('Jurnal manual dihapus.'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        $sekolahId = $this->resolveSekolahId($request->user());

        [$tanggalFrom, $tanggalTo] = $this->tanggalRange($request);

        $jurnals = ManualJurnalService::jurnalsForExport($sekolahId, $tanggalFrom, $tanggalTo);

        $filename = 'jurnal-'.$tanggalFrom.'-'.$tanggalTo.'-'.now()->format('His').'.csv';

        return response()->streamDownload(function () use ($jurnals): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['jurnal_id', 'tanggal', 'no_bukti', 'keterangan', 'sumber', 'akun_kode', 'akun_nama', 'debit', 'kredit']);

            foreach ($jurnals as $j) {
                $sumber = ManualJurnalService::sumberLabel($j->sumber_type);
                foreach ($j->lines as $line) {
                    fputcsv($out, [
                        $j->id,
                        DateTimeFormat::date($j->tanggal),
                        $j->no_bukti ?? '',
                        $j->keterangan ?? '',
                        $sumber,
                        $line->akun?->kode ?? '',
                        $line->akun?->nama ?? '',
                        number_format((float) $line->debit, 2, '.', ''),
                        number_format((float) $line->kredit, 2, '.', ''),
                    ]);
                }
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function resolveSekolahId(User $user): int
    {
        return KeuanganTenant::sekolahId($user);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function tanggalRange(Request $request): array
    {
        $from = $request->filled('tanggal_from')
            ? Carbon::parse($request->string('tanggal_from'))->toDateString()
            : now()->subMonths(2)->startOfMonth()->toDateString();
        $to = $request->filled('tanggal_to')
            ? Carbon::parse($request->string('tanggal_to'))->toDateString()
            : now()->toDateString();

        if (strcmp($from, $to) > 0) {
            return [$to, $from];
        }

        return [$from, $to];
    }
}
