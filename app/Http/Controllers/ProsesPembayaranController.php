<?php

namespace App\Http\Controllers;

use App\Models\KewajibanPembayaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Support\KeuanganTenant;
use App\Support\PembayaranService;
use App\Support\TagihanGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProsesPembayaranController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $siswaId = $request->query('siswa_id');
        $bulan = (int) ($request->query('bulan') ?: now()->month);
        $tahun = (int) ($request->query('tahun') ?: now()->year);

        $siswaOptions = Siswa::query()
            ->with('kelas:id,tingkat,nama')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis', 'kelas_id']);

        $selectedSiswa = null;
        $periode = sprintf('%04d-%02d', $tahun, $bulan);
        $tagihans = collect();
        $outstandingDiPeriodeLain = collect();

        if ($siswaId) {
            $selectedSiswa = Siswa::query()->whereKey($siswaId)->first();
            if ($selectedSiswa) {
                $tagihans = Tagihan::query()
                    ->where('siswa_id', $selectedSiswa->id)
                    ->where('periode', $periode)
                    ->withSum('pembayarans as total_dibayar', 'jumlah')
                    ->orderBy('jenis')
                    ->get();

                $outstandingDiPeriodeLain = Tagihan::query()
                    ->where('siswa_id', $selectedSiswa->id)
                    ->where('periode', '!=', $periode)
                    ->withSum('pembayarans as total_dibayar', 'jumlah')
                    ->orderBy('periode')
                    ->orderBy('jenis')
                    ->limit(80)
                    ->get()
                    ->map(function (Tagihan $t): ?array {
                        $dibayar = (float) ($t->total_dibayar ?? 0);
                        $sisa = max(0, (float) $t->jumlah - $dibayar);

                        return $sisa > 0.00001 ? ['tagihan' => $t, 'sisa' => $sisa] : null;
                    })
                    ->filter()
                    ->values();
            }
        }

        $kewajibanActive = KewajibanPembayaran::query()
            ->where('is_active', true)
            ->orderBy('tipe')
            ->orderBy('nama')
            ->get(['id', 'nama', 'tipe', 'nominal_default', 'batas_hari_bayar']);

        $kewajibanInsidental = KewajibanPembayaran::query()
            ->where('is_active', true)
            ->where('tipe', 'insidental')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nominal_default']);

        $kelasOptions = Kelas::query()
            ->where('is_active', true)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama']);

        return view('keuangan.proses.index', compact(
            'siswaOptions',
            'selectedSiswa',
            'periode',
            'bulan',
            'tahun',
            'tagihans',
            'kewajibanActive',
            'outstandingDiPeriodeLain',
            'kelasOptions',
            'kewajibanInsidental',
        ));
    }

    public function generate(Request $request): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $data = $request->validate([
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $periode = sprintf('%04d-%02d', (int) $data['tahun'], (int) $data['bulan']);
        $siswa = Siswa::query()->whereKey((int) $data['siswa_id'])->firstOrFail();

        $kewajiban = KewajibanPembayaran::query()
            ->where('is_active', true)
            ->where('tipe', 'bulanan')
            ->orderBy('nama')
            ->get();

        DB::transaction(function () use ($kewajiban, $siswa, $periode, $data): void {
            foreach ($kewajiban as $k) {
                $exists = Tagihan::query()
                    ->where('siswa_id', $siswa->id)
                    ->where('periode', $periode)
                    ->where('jenis', $k->nama)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $jatuhTempo = null;
                if ($k->batas_hari_bayar) {
                    $jatuhTempo = Carbon::create((int) $data['tahun'], (int) $data['bulan'], (int) $k->batas_hari_bayar)->toDateString();
                }

                Tagihan::query()->create([
                    'siswa_id' => $siswa->id,
                    'jenis' => $k->nama,
                    'periode' => $periode,
                    'jumlah' => (float) $k->nominal_default,
                    'jatuh_tempo' => $jatuhTempo,
                    'status' => 'unpaid',
                ]);
            }
        });

        return redirect()
            ->route('keuangan.proses.index', ['siswa_id' => $siswa->id, 'bulan' => (int) $data['bulan'], 'tahun' => (int) $data['tahun']])
            ->with('status', __('Tagihan bulanan dibuat (yang belum ada).'));
    }

    public function generateMass(Request $request): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $data = $request->validate([
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $periode = sprintf('%04d-%02d', (int) $data['tahun'], (int) $data['bulan']);
        $sekolahId = KeuanganTenant::sekolahId(auth()->user());

        $res = TagihanGenerator::generateBulananForSekolah(
            $periode,
            $sekolahId,
            ! empty($data['kelas_id']) ? (int) $data['kelas_id'] : null
        );

        $label = ! empty($data['kelas_id'])
            ? __('kelas terpilih')
            : __('semua siswa');

        return redirect()
            ->route('keuangan.proses.index', ['bulan' => (int) $data['bulan'], 'tahun' => (int) $data['tahun']])
            ->with('status', __('Generate massal selesai untuk :label. Dibuat: :c, dilewati (sudah ada): :s.', [
                'label' => $label,
                'c' => $res['created'],
                's' => $res['skipped'],
            ]));
    }

    public function generateInsidentalMass(Request $request): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $data = $request->validate([
            'kewajiban_id' => ['required', 'integer', 'exists:kewajiban_pembayarans,id'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'nominal' => ['nullable', 'numeric', 'min:0'],
            'jatuh_tempo' => ['nullable', 'date'],
        ]);

        /** @var KewajibanPembayaran $kewajiban */
        $kewajiban = KewajibanPembayaran::query()->whereKey((int) $data['kewajiban_id'])->firstOrFail();
        if ($kewajiban->tipe !== 'insidental') {
            abort(422, __('Kewajiban harus bertipe insidental.'));
        }

        $periode = sprintf('%04d-%02d', (int) $data['tahun'], (int) $data['bulan']);
        $jumlah = array_key_exists('nominal', $data) && $data['nominal'] !== null
            ? (float) $data['nominal']
            : (float) $kewajiban->nominal_default;

        $jatuhTempo = $data['jatuh_tempo'] ?? null;

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($kewajiban, $periode, $jumlah, $jatuhTempo, $data, &$created, &$skipped): void {
            $siswaQ = Siswa::query()->select(['id'])->orderBy('id');
            if (! empty($data['kelas_id'])) {
                $siswaQ->where('kelas_id', (int) $data['kelas_id']);
            }

            $siswaQ->chunkById(200, function ($siswas) use ($kewajiban, $periode, $jumlah, $jatuhTempo, &$created, &$skipped): void {
                foreach ($siswas as $s) {
                    $exists = Tagihan::query()
                        ->where('siswa_id', $s->id)
                        ->where('periode', $periode)
                        ->where('jenis', $kewajiban->nama)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    Tagihan::query()->create([
                        'siswa_id' => $s->id,
                        'jenis' => $kewajiban->nama,
                        'periode' => $periode,
                        'jumlah' => $jumlah,
                        'jatuh_tempo' => $jatuhTempo,
                        'status' => 'unpaid',
                    ]);
                    $created++;
                }
            });
        });

        $label = ! empty($data['kelas_id'])
            ? __('kelas terpilih')
            : __('semua siswa');

        return redirect()
            ->route('keuangan.proses.index', ['bulan' => (int) $data['bulan'], 'tahun' => (int) $data['tahun']])
            ->with('status', __('Tagihan insidental ":nama" dibuat untuk :label. Dibuat: :c, dilewati (sudah ada): :s.', [
                'nama' => $kewajiban->nama,
                'label' => $label,
                'c' => $created,
                's' => $skipped,
            ]));
    }

    public function bayar(Request $request): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $data = $request->validate([
            'tagihan_ids' => ['required', 'array', 'min:1'],
            'tagihan_ids.*' => ['integer', 'exists:tagihans,id'],
            'amounts' => ['nullable', 'array'],
            'amounts.*' => ['nullable', 'numeric', 'min:0.01'],
            'metode' => ['required', 'string', 'max:32', Rule::in(\App\Models\Pembayaran::METODE_OPTIONS)],
            'referensi' => ['nullable', 'string', 'max:255'],
            'dibayar_pada' => ['nullable', 'date'],
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $periode = sprintf('%04d-%02d', (int) $data['tahun'], (int) $data['bulan']);
        $siswa = Siswa::query()->whereKey((int) $data['siswa_id'])->firstOrFail();
        $userId = (int) $request->user()->id;

        DB::transaction(function () use ($data, $siswa, $periode, $userId): void {
            $tagihans = Tagihan::query()
                ->whereIn('id', $data['tagihan_ids'])
                ->where('siswa_id', $siswa->id)
                ->where('periode', $periode)
                ->withSum('pembayarans as total_dibayar', 'jumlah')
                ->lockForUpdate()
                ->get();

            foreach ($tagihans as $t) {
                $dibayar = (float) ($t->total_dibayar ?? 0);
                $sisa = max(0, (float) $t->jumlah - $dibayar);
                if ($sisa <= 0.00001) {
                    continue;
                }

                $nominal = $sisa;
                if (! empty($data['amounts']) && array_key_exists((string) $t->id, $data['amounts'])) {
                    $raw = $data['amounts'][(string) $t->id];
                    if ($raw !== null && $raw !== '') {
                        $nominal = (float) $raw;
                    }
                }

                $nominal = max(0, min($nominal, $sisa));
                if ($nominal <= 0.00001) {
                    continue;
                }

                PembayaranService::record($t, [
                    'jumlah' => $nominal,
                    'metode' => $data['metode'],
                    'referensi' => $data['referensi'] ?? null,
                    'dibayar_pada' => $data['dibayar_pada'] ?? null,
                ], $userId);
            }
        });

        return redirect()
            ->route('keuangan.proses.index', ['siswa_id' => $siswa->id, 'bulan' => (int) $data['bulan'], 'tahun' => (int) $data['tahun']])
            ->with('status', __('Pembayaran dicatat untuk tagihan terpilih.'));
    }
}

