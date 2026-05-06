<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresensiGuruBulkRequest;
use App\Models\Guru;
use App\Models\PresensiGuru;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PresensiGuruController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', PresensiGuru::class);

        $tanggal = request('tanggal');

        $rows = PresensiGuru::query()
            ->with(['guru:id,nama,nip'])
            ->when($tanggal, fn ($q) => $q->whereDate('tanggal', $tanggal))
            ->orderByDesc('tanggal')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        return view('presensi.guru.index', compact('rows', 'tanggal'));
    }

    public function create(): View
    {
        Gate::authorize('create', PresensiGuru::class);

        $tanggal = request('tanggal', now()->toDateString());

        $gurus = Guru::query()->orderBy('nama')->get(['id', 'nama', 'nip']);

        $existing = PresensiGuru::query()
            ->where('tanggal', $tanggal)
            ->whereIn('guru_id', $gurus->pluck('id'))
            ->get()
            ->keyBy('guru_id');

        return view('presensi.guru.create', compact('tanggal', 'gurus', 'existing'));
    }

    public function store(StorePresensiGuruBulkRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            foreach ($data['presensi'] as $row) {
                PresensiGuru::query()->updateOrCreate(
                    [
                        'guru_id' => $row['guru_id'],
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
            ->route('presensi.guru.create', ['tanggal' => $data['tanggal']])
            ->with('status', __('Presensi guru disimpan.'));
    }

    public function destroy(PresensiGuru $presensiGuru): RedirectResponse
    {
        Gate::authorize('delete', $presensiGuru);

        $presensiGuru->delete();

        return redirect()
            ->route('presensi.guru.index')
            ->with('status', __('Baris presensi dihapus.'));
    }
}
