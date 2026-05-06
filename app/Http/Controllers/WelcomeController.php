<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\InventarisBarang;
use App\Models\Kelas;
use App\Models\Perizinan;
use App\Models\PpdbRegistration;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            [
                'label' => __('Lembaga'),
                'value' => (int) Sekolah::query()->where('is_active', true)->count(),
            ],
            [
                'label' => __('Siswa'),
                'value' => (int) Siswa::query()->count(),
            ],
            [
                'label' => __('Guru'),
                'value' => (int) Guru::query()->count(),
            ],
            [
                'label' => __('Kelas aktif'),
                'value' => (int) Kelas::query()->where('is_active', true)->count(),
            ],
            [
                'label' => __('Tagihan belum lunas'),
                'value' => (int) Tagihan::query()->whereIn('status', ['unpaid', 'partial'])->count(),
            ],
        ];

        $highlights = [
            'ppdb_pending' => (int) PpdbRegistration::query()->whereIn('status', ['submitted', 'verified'])->count(),
            'perizinan_pending' => (int) Perizinan::query()->where('status', 'pending')->count(),
            'stok_minimum' => (int) InventarisBarang::query()
                ->where('is_active', true)
                ->withSum(['mutasis as sum_in' => fn ($q) => $q->where('tipe', 'in')], 'jumlah')
                ->withSum(['mutasis as sum_out' => fn ($q) => $q->where('tipe', 'out')], 'jumlah')
                ->withSum(['mutasis as sum_adjust' => fn ($q) => $q->where('tipe', 'adjust')], 'jumlah')
                ->get(['id', 'stok_awal', 'stok_minimum'])
                ->filter(function (InventarisBarang $b): bool {
                    $stokAkhir = (int) $b->stok_awal
                        + (int) ($b->sum_in ?? 0)
                        - (int) ($b->sum_out ?? 0)
                        + (int) ($b->sum_adjust ?? 0);

                    return $stokAkhir <= (int) $b->stok_minimum;
                })
                ->count(),
        ];

        $kelasTerpadat = Kelas::query()
            ->withCount('siswas')
            ->orderByDesc('siswas_count')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->take(3)
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran']);

        $distribusiTingkat = Kelas::query()
            ->select('tingkat', DB::raw('count(*) as jumlah_kelas'))
            ->where('is_active', true)
            ->groupBy('tingkat')
            ->orderBy('tingkat')
            ->get()
            ->map(fn ($r) => ['tingkat' => (int) $r->tingkat, 'jumlah_kelas' => (int) $r->jumlah_kelas])
            ->all();

        $siswaPerTingkat = Kelas::query()
            ->leftJoin('siswas', 'siswas.kelas_id', '=', 'kelas.id')
            ->select('kelas.tingkat', DB::raw('count(siswas.id) as jumlah_siswa'))
            ->where('kelas.is_active', true)
            ->groupBy('kelas.tingkat')
            ->orderBy('kelas.tingkat')
            ->get()
            ->map(fn ($r) => ['tingkat' => (int) $r->tingkat, 'jumlah_siswa' => (int) $r->jumlah_siswa])
            ->all();

        $totalSiswaAktif = array_sum(array_map(fn ($r) => (int) $r['jumlah_siswa'], $siswaPerTingkat));
        $distribusiJenjang = collect($siswaPerTingkat)
            ->sortByDesc('jumlah_siswa')
            ->take(4)
            ->values()
            ->map(function (array $r) use ($totalSiswaAktif) {
                $pct = $totalSiswaAktif > 0 ? (int) round(((int) $r['jumlah_siswa'] / $totalSiswaAktif) * 100) : 0;

                return [
                    'tingkat' => (int) $r['tingkat'],
                    'jumlah_siswa' => (int) $r['jumlah_siswa'],
                    'pct' => $pct,
                ];
            })
            ->all();

        return view('welcome', compact(
            'stats',
            'highlights',
            'kelasTerpadat',
            'distribusiTingkat',
            'siswaPerTingkat',
            'distribusiJenjang',
            'totalSiswaAktif'
        ));
    }
}
