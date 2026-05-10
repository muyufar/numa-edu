<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\InventarisBarang;
use App\Models\InventarisMutasi;
use App\Models\Kelas;
use App\Models\LembagaRegistration;
use App\Models\Perizinan;
use App\Models\PpdbRegistration;
use App\Models\PresensiGuru;
use App\Models\PresensiPegawai;
use App\Models\PresensiSiswa;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use App\Support\PengurusCabangOverview;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $canAkademik = $user?->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']) ?? false;
        $canKeuangan = $user?->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang']) ?? false;
        $canOperasional = $user?->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']) ?? false;

        $allModules = [
            [
                'title' => 'Akademik',
                'description' => 'Data siswa, guru, kelas, mapel, jadwal, nilai, rapor.',
                'roles' => ['super_admin', 'admin', 'guru', 'pengurus_cabang'],
            ],
            [
                'title' => 'Keuangan',
                'description' => 'Tagihan SPP, pembayaran, laporan keuangan.',
                'roles' => ['super_admin', 'admin', 'pengurus_cabang'],
            ],
            [
                'title' => 'Absensi',
                'description' => 'Presensi siswa & guru (hadir, izin, sakit, alpha).',
                'roles' => ['super_admin', 'admin', 'guru', 'pengurus_cabang'],
            ],
            [
                'title' => 'BK',
                'description' => 'Pelanggaran & tindakan pembinaan.',
                'roles' => ['super_admin', 'admin', 'guru', 'pengurus_cabang'],
            ],
            [
                'title' => 'PPDB',
                'description' => 'Pendaftaran, verifikasi, masuk ke data utama.',
                'roles' => ['super_admin', 'admin', 'pengurus_cabang'],
            ],
            [
                'title' => 'Wali murid',
                'description' => 'Pantau absensi, nilai, dan tagihan anak.',
                'roles' => ['wali'],
            ],
            [
                'title' => 'Siswa',
                'description' => 'Jadwal, nilai, dan informasi pribadi.',
                'roles' => ['siswa'],
            ],
        ];

        $modules = array_values(array_filter($allModules, function (array $m) use ($user) {
            foreach ($m['roles'] as $role) {
                if ($user->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }));

        $today = now()->toDateString();

        $lembagaRegPendingCount = 0;
        if ($user->hasAnyRole(['super_admin', 'pengurus_cabang'])) {
            $lrq = LembagaRegistration::query()->where('status', LembagaRegistration::STATUS_PENDING_REVIEW);
            if ($user->hasRole('pengurus_cabang') && $user->cabang_id) {
                $lrq->where('cabang_id', $user->cabang_id);
            }
            $lembagaRegPendingCount = (int) $lrq->count();
        }

        $stats = collect()
            ->when($canAkademik, function ($c) {
                return $c->push(
                    [
                        'label' => __('Siswa'),
                        'value' => (string) Siswa::query()->count(),
                        'hint' => __('Total data siswa'),
                    ],
                    [
                        'label' => __('Guru'),
                        'value' => (string) Guru::query()->count(),
                        'hint' => __('Profil pengajar'),
                    ],
                    [
                        'label' => __('Kelas aktif'),
                        'value' => (string) Kelas::query()->where('is_active', true)->count(),
                        'hint' => __('Rombel berjalan'),
                    ],
                );
            })
            ->when($canKeuangan, function ($c) {
                return $c->push([
                    'label' => __('Tagihan belum lunas'),
                    'value' => (string) Tagihan::query()->whereIn('status', ['unpaid', 'partial'])->count(),
                    'hint' => __('Unpaid + partial'),
                ]);
            })
            ->when($canOperasional, function ($c) {
                return $c->push([
                    'label' => __('Perizinan pending'),
                    'value' => (string) Perizinan::query()->where('status', 'pending')->count(),
                    'hint' => __('Menunggu ditinjau'),
                ]);
            })
            ->when($lembagaRegPendingCount > 0 && $user->hasAnyRole(['super_admin', 'pengurus_cabang']), function ($c) use ($lembagaRegPendingCount) {
                return $c->push([
                    'label' => __('Pendaftaran lembaga'),
                    'value' => (string) $lembagaRegPendingCount,
                    'hint' => __('Menunggu verifikasi PCNU'),
                ]);
            })
            ->when($canOperasional, function ($c) {
                return $c->push([
                    'label' => __('Stok menipis'),
                    'value' => (string) InventarisBarang::query()
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
                    'hint' => __('<= stok minimum'),
                ]);
            })
            ->values()
            ->all();

        $recentUsers = $user?->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang'])
            ? User::query()->latest()->take(6)->get(['name', 'email', 'created_at'])
            : collect();

        $recentPerizinan = $canOperasional
            ? Perizinan::query()
                ->with('siswa')
                ->latest()
                ->take(6)
                ->get()
            : collect();

        $recentMutasi = $canOperasional
            ? InventarisMutasi::query()
                ->with('barang')
                ->latest()
                ->take(6)
                ->get()
            : collect();

        $recentPpdb = $user?->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang'])
            ? PpdbRegistration::query()->latest()->take(6)->get()
            : collect();

        $presensiToday = $canOperasional
            ? [
                'siswa' => PresensiSiswa::query()->whereDate('tanggal', $today)->count(),
                'guru' => PresensiGuru::query()->whereDate('tanggal', $today)->count(),
                'pegawai' => PresensiPegawai::query()->whereDate('tanggal', $today)->count(),
            ]
            : null;

        $pengurusOverview = null;
        $pengurusRekapPaginator = null;
        if ($user->hasRole('pengurus_cabang') && $user->cabang_id) {
            $pengurusOverview = PengurusCabangOverview::build((int) $user->cabang_id);
        } elseif ($user->hasRole('super_admin')) {
            $pengurusOverview = PengurusCabangOverview::build(null);
        }

        if ($pengurusOverview !== null) {
            $rekap = collect($pengurusOverview['rekap_rows']);
            $perPage = 10;
            $page = max(1, (int) request()->query('kec_page', 1));
            $pengurusRekapPaginator = (new LengthAwarePaginator(
                $rekap->forPage($page, $perPage)->values()->all(),
                $rekap->count(),
                $perPage,
                $page,
                [
                    'path' => request()->url(),
                    'pageName' => 'kec_page',
                ]
            ))->withQueryString();
        }

        $days = collect(range(6, 0))
            ->map(fn ($i) => Carbon::today()->subDays($i)->toDateString());

        $siswa7d = $canAkademik
            ? $days->map(function (string $d) {
                return [
                    'date' => $d,
                    'count' => (int) Siswa::query()->whereDate('created_at', $d)->count(),
                ];
            })->all()
            : [];

        return view('dashboard', compact(
            'modules',
            'stats',
            'recentUsers',
            'recentPerizinan',
            'recentMutasi',
            'recentPpdb',
            'presensiToday',
            'siswa7d',
            'pengurusOverview',
            'pengurusRekapPaginator',
            'lembagaRegPendingCount',
        ));
    }
}
