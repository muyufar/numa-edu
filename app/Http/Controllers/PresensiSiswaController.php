<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresensiSiswaBulkRequest;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\PresensiSiswa;
use App\Models\Siswa;
use App\Support\PolicyRoles;
use App\Support\SekolahPresensiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PresensiSiswaController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', PresensiSiswa::class);

        $user = auth()->user();
        $isSiswaView = PolicyRoles::siswaTerhubung($user);
        $ownSiswaId = $isSiswaView ? (int) $user->siswa->id : null;

        $perMapel = SekolahPresensiSettings::isPerMapel();
        $kelasId = $isSiswaView ? null : request('kelas_id');
        $jadwalId = request('jadwal_id');
        $tanggal = request('tanggal');

        $rows = PresensiSiswa::query()
            ->with(['siswa.kelas:id,tingkat,nama', 'jadwal.mataPelajaran:id,nama'])
            ->when($ownSiswaId, fn ($q) => $q->where('siswa_id', $ownSiswaId))
            ->when($kelasId, function ($q) use ($kelasId): void {
                $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId));
            })
            ->when($jadwalId, fn ($q) => $q->where('jadwal_id', $jadwalId))
            ->when($tanggal, fn ($q) => $q->whereDate('tanggal', $tanggal))
            ->orderByDesc('tanggal')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $filterKelasOptions = $isSiswaView
            ? collect()
            : Kelas::query()
                ->orderByDesc('is_active')
                ->orderByDesc('tahun_ajaran')
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $filterJadwalOptions = collect();
        if ($perMapel && $kelasId && $tanggal) {
            $filterJadwalOptions = $this->jadwalOptionsFor((int) $kelasId, $tanggal);
        }

        return view('presensi.siswa.index', compact(
            'rows',
            'filterKelasOptions',
            'filterJadwalOptions',
            'kelasId',
            'jadwalId',
            'tanggal',
            'perMapel',
            'isSiswaView'
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', PresensiSiswa::class);

        $perMapel = SekolahPresensiSettings::isPerMapel();

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasId = request('kelas_id');
        $jadwalId = request('jadwal_id');
        $tanggal = request('tanggal', now()->toDateString());

        $jadwalOptions = collect();
        $siswas = collect();
        $existing = collect();

        if ($kelasId) {
            if ($perMapel) {
                $jadwalOptions = $this->jadwalOptionsFor((int) $kelasId, $tanggal);
            }

            if (! $perMapel || $jadwalId) {
                $siswas = Siswa::query()
                    ->where('kelas_id', $kelasId)
                    ->orderBy('nama')
                    ->get(['id', 'nama', 'nis']);

                $slot = SekolahPresensiSettings::slotForJadwal($perMapel ? (int) $jadwalId : null);

                $existing = PresensiSiswa::query()
                    ->where('tanggal', $tanggal)
                    ->where('presensi_slot', $slot)
                    ->whereIn('siswa_id', $siswas->pluck('id'))
                    ->get()
                    ->keyBy('siswa_id');
            }
        }

        return view('presensi.siswa.create', compact(
            'kelasOptions',
            'kelasId',
            'jadwalId',
            'jadwalOptions',
            'tanggal',
            'siswas',
            'existing',
            'perMapel'
        ));
    }

    public function store(StorePresensiSiswaBulkRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $perMapel = SekolahPresensiSettings::isPerMapel();
        $jadwalId = $perMapel ? (int) $data['jadwal_id'] : null;
        $slot = SekolahPresensiSettings::slotForJadwal($jadwalId);

        DB::transaction(function () use ($data, $jadwalId, $slot): void {
            foreach ($data['presensi'] as $row) {
                PresensiSiswa::query()->updateOrCreate(
                    [
                        'siswa_id' => $row['siswa_id'],
                        'tanggal' => $data['tanggal'],
                        'presensi_slot' => $slot,
                    ],
                    [
                        'jadwal_id' => $jadwalId,
                        'status' => $row['status'],
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                );
            }
        });

        $params = [
            'kelas_id' => $data['kelas_id'],
            'tanggal' => $data['tanggal'],
        ];
        if ($perMapel) {
            $params['jadwal_id'] = $data['jadwal_id'];
        }

        return redirect()
            ->route('presensi.siswa.create', $params)
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

    private function jadwalOptionsFor(int $kelasId, string $tanggal)
    {
        return Jadwal::query()
            ->with('mataPelajaran:id,nama')
            ->where('kelas_id', $kelasId)
            ->where('hari', Jadwal::hariFromDate($tanggal))
            ->ordered()
            ->get();
    }
}
