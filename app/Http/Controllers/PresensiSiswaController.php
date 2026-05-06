<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresensiSiswaBulkRequest;
use App\Models\Kelas;
use App\Models\PresensiSiswa;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PresensiSiswaController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', PresensiSiswa::class);

        $kelasId = request('kelas_id');
        $tanggal = request('tanggal');

        $rows = PresensiSiswa::query()
            ->with(['siswa.kelas:id,tingkat,nama'])
            ->when($kelasId, function ($q) use ($kelasId): void {
                $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId));
            })
            ->when($tanggal, fn ($q) => $q->whereDate('tanggal', $tanggal))
            ->orderByDesc('tanggal')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $filterKelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        return view('presensi.siswa.index', compact('rows', 'filterKelasOptions', 'kelasId', 'tanggal'));
    }

    public function create(): View
    {
        Gate::authorize('create', PresensiSiswa::class);

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasId = request('kelas_id');
        $tanggal = request('tanggal', now()->toDateString());

        $siswas = collect();
        $existing = collect();

        if ($kelasId) {
            $siswas = Siswa::query()
                ->where('kelas_id', $kelasId)
                ->orderBy('nama')
                ->get(['id', 'nama', 'nis']);

            $existing = PresensiSiswa::query()
                ->where('tanggal', $tanggal)
                ->whereIn('siswa_id', $siswas->pluck('id'))
                ->get()
                ->keyBy('siswa_id');
        }

        return view('presensi.siswa.create', compact(
            'kelasOptions',
            'kelasId',
            'tanggal',
            'siswas',
            'existing'
        ));
    }

    public function store(StorePresensiSiswaBulkRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            foreach ($data['presensi'] as $row) {
                PresensiSiswa::query()->updateOrCreate(
                    [
                        'siswa_id' => $row['siswa_id'],
                        'tanggal' => $data['tanggal'],
                    ],
                    [
                        'status' => $row['status'],
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('presensi.siswa.create', ['kelas_id' => $data['kelas_id'], 'tanggal' => $data['tanggal']])
            ->with('status', __('Presensi siswa disimpan.'));
    }

    public function destroy(PresensiSiswa $presensiSiswa): RedirectResponse
    {
        Gate::authorize('delete', $presensiSiswa);

        $presensiSiswa->delete();

        return redirect()
            ->route('presensi.siswa.index')
            ->with('status', __('Baris presensi dihapus.'));
    }
}
