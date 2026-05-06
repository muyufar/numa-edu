<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresensiPegawaiBulkRequest;
use App\Models\Pegawai;
use App\Models\PresensiPegawai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PresensiPegawaiController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', PresensiPegawai::class);

        $tanggal = request('tanggal');

        $rows = PresensiPegawai::query()
            ->with(['pegawai:id,nama,nip'])
            ->when($tanggal, fn ($q) => $q->whereDate('tanggal', $tanggal))
            ->orderByDesc('tanggal')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        return view('presensi.pegawai.index', compact('rows', 'tanggal'));
    }

    public function create(): View
    {
        Gate::authorize('create', PresensiPegawai::class);

        $tanggal = request('tanggal', now()->toDateString());

        $pegawais = Pegawai::query()
            ->where('is_active', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip']);

        $existing = PresensiPegawai::query()
            ->where('tanggal', $tanggal)
            ->whereIn('pegawai_id', $pegawais->pluck('id'))
            ->get()
            ->keyBy('pegawai_id');

        return view('presensi.pegawai.create', compact('tanggal', 'pegawais', 'existing'));
    }

    public function store(StorePresensiPegawaiBulkRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            foreach ($data['presensi'] as $row) {
                PresensiPegawai::query()->updateOrCreate(
                    [
                        'pegawai_id' => $row['pegawai_id'],
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
            ->route('presensi.pegawai.create', ['tanggal' => $data['tanggal']])
            ->with('status', __('Presensi pegawai disimpan.'));
    }

    public function destroy(PresensiPegawai $presensiPegawai): RedirectResponse
    {
        Gate::authorize('delete', $presensiPegawai);

        $presensiPegawai->delete();

        return redirect()
            ->route('presensi.pegawai.index')
            ->with('status', __('Baris presensi dihapus.'));
    }
}
