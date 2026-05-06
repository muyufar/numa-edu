<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagihanRequest;
use App\Http\Requests\UpdateTagihanRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $siswaId = request('siswa_id');
        $status = request('status');
        $periodeFrom = request('periode_from');
        $periodeTo = request('periode_to');
        $kelasId = request('kelas_id');

        $tagihans = Tagihan::query()
            ->with(['siswa:id,nama,nis,kelas_id', 'siswa.kelas:id,tingkat,nama'])
            ->withSum('pembayarans as total_dibayar', 'jumlah')
            ->when($siswaId, fn ($q) => $q->where('siswa_id', $siswaId))
            ->when($kelasId, fn ($q) => $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId)))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($periodeFrom, fn ($q) => $q->where('periode', '>=', $periodeFrom))
            ->when($periodeTo, fn ($q) => $q->where('periode', '<=', $periodeTo))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $siswaFilterOptions = Siswa::query()
            ->with('kelas:id,tingkat,nama')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis', 'kelas_id']);

        $kelasOptions = Kelas::query()
            ->where('is_active', true)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran']);

        return view('tagihan.index', compact(
            'tagihans',
            'siswaFilterOptions',
            'kelasOptions',
            'siswaId',
            'kelasId',
            'status',
            'periodeFrom',
            'periodeTo',
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', Tagihan::class);

        $siswaOptions = Siswa::query()
            ->with('kelas:id,tingkat,nama')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis', 'kelas_id']);

        return view('tagihan.create', compact('siswaOptions'));
    }

    public function store(StoreTagihanRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = 'unpaid';

        $tagihan = Tagihan::query()->create($data);

        return redirect()
            ->route('tagihan.show', $tagihan)
            ->with('status', __('Tagihan berhasil dibuat.'));
    }

    public function show(Tagihan $tagihan): View
    {
        Gate::authorize('view', $tagihan);

        $tagihan->load([
            'siswa.kelas:id,tingkat,nama',
            'pembayarans' => fn ($q) => $q->orderByDesc('dibayar_pada')->orderByDesc('id'),
            'pembayarans.dicatatOleh:id,name',
        ]);

        $sisa = $tagihan->sisa();

        return view('tagihan.show', compact('tagihan', 'sisa'));
    }

    public function edit(Tagihan $tagihan): View
    {
        Gate::authorize('update', $tagihan);

        $tagihan->load('siswa:id,nama,nis,kelas_id');

        $siswaOptions = Siswa::query()
            ->with('kelas:id,tingkat,nama')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis', 'kelas_id']);

        return view('tagihan.edit', compact('tagihan', 'siswaOptions'));
    }

    public function update(UpdateTagihanRequest $request, Tagihan $tagihan): RedirectResponse
    {
        $tagihan->update($request->validated());
        $tagihan->refreshStatus();

        return redirect()
            ->route('tagihan.show', $tagihan)
            ->with('status', __('Tagihan diperbarui.'));
    }

    public function destroy(Tagihan $tagihan): RedirectResponse
    {
        Gate::authorize('delete', $tagihan);

        $tagihan->delete();

        return redirect()
            ->route('tagihan.index')
            ->with('status', __('Tagihan dihapus.'));
    }
}
