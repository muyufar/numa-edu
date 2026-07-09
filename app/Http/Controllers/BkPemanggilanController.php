<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBkPemanggilanRequest;
use App\Http\Requests\UpdateBkPemanggilanRequest;
use App\Models\BkPemanggilan;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BkPemanggilanController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', BkPemanggilan::class);

        $kelasId = request('kelas_id');
        $status = request('status');

        $rows = BkPemanggilan::query()
            ->with(['siswa.kelas:id,tingkat,nama,tahun_ajaran', 'dicatatOleh:id,name'])
            ->when($kelasId, fn ($q) => $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId)))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('tanggal_jadwal')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $filterKelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        return view('bk.pemanggilan.index', compact('rows', 'filterKelasOptions', 'kelasId', 'status'));
    }

    public function create(): View
    {
        Gate::authorize('create', BkPemanggilan::class);

        return view('bk.pemanggilan.create', $this->formContext());
    }

    public function store(StoreBkPemanggilanRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except('kelas_id')->all();
        $data['dicatat_oleh'] = $request->user()->id;

        BkPemanggilan::query()->create($data);

        return redirect()
            ->route('bk.pemanggilan.index')
            ->with('status', __('Pemanggilan dijadwalkan.'));
    }

    public function edit(BkPemanggilan $bk_pemanggilan): View
    {
        Gate::authorize('update', $bk_pemanggilan);

        $bk_pemanggilan->load(['siswa.kelas:id,tingkat,nama,tahun_ajaran']);

        return view('bk.pemanggilan.edit', array_merge(
            ['row' => $bk_pemanggilan],
            $this->formContext($bk_pemanggilan),
        ));
    }

    public function update(UpdateBkPemanggilanRequest $request, BkPemanggilan $bk_pemanggilan): RedirectResponse
    {
        $bk_pemanggilan->update($request->validated());

        return redirect()
            ->route('bk.pemanggilan.index')
            ->with('status', __('Pemanggilan diperbarui.'));
    }

    public function destroy(BkPemanggilan $bk_pemanggilan): RedirectResponse
    {
        Gate::authorize('delete', $bk_pemanggilan);

        $bk_pemanggilan->delete();

        return redirect()
            ->route('bk.pemanggilan.index')
            ->with('status', __('Pemanggilan dihapus.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formContext(?BkPemanggilan $row = null): array
    {
        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasId = old('kelas_id', request('kelas_id', $row?->siswa?->kelas_id));
        $siswas = $kelasId
            ? Siswa::query()->where('kelas_id', $kelasId)->orderBy('nama')->get(['id', 'nama', 'nis'])
            : collect();

        if ($siswas->isEmpty() && $row?->siswa_id) {
            $siswas = Siswa::query()->whereKey($row->siswa_id)->get(['id', 'nama', 'nis']);
        }

        return compact('kelasOptions', 'kelasId', 'siswas');
    }
}
