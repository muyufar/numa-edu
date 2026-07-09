<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRewardSiswaRequest;
use App\Http\Requests\UpdateRewardSiswaRequest;
use App\Models\Kelas;
use App\Models\RewardSiswa;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RewardSiswaController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', RewardSiswa::class);

        $kelasId = request('kelas_id');
        $siswaId = request('siswa_id');

        $rows = RewardSiswa::query()
            ->with(['siswa.kelas:id,tingkat,nama,tahun_ajaran', 'dicatatOleh:id,name'])
            ->when($kelasId, fn ($q) => $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId)))
            ->when($siswaId, fn ($q) => $q->where('siswa_id', $siswaId))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $filterKelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $siswaFilterOptions = $kelasId
            ? Siswa::query()->where('kelas_id', $kelasId)->orderBy('nama')->get(['id', 'nama', 'nis'])
            : collect();

        return view('kesiswaan.reward.index', compact(
            'rows',
            'filterKelasOptions',
            'siswaFilterOptions',
            'kelasId',
            'siswaId',
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', RewardSiswa::class);

        return view('kesiswaan.reward.create', $this->formContext());
    }

    public function store(StoreRewardSiswaRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except('kelas_id')->all();
        $data['dicatat_oleh'] = $request->user()->id;

        RewardSiswa::query()->create($data);

        return redirect()
            ->route('kesiswaan.reward.index')
            ->with('status', __('Reward dicatat.'));
    }

    public function edit(RewardSiswa $reward_siswa): View
    {
        Gate::authorize('update', $reward_siswa);

        $reward_siswa->load(['siswa.kelas:id,tingkat,nama,tahun_ajaran']);

        return view('kesiswaan.reward.edit', array_merge(
            ['row' => $reward_siswa],
            $this->formContext($reward_siswa),
        ));
    }

    public function update(UpdateRewardSiswaRequest $request, RewardSiswa $reward_siswa): RedirectResponse
    {
        $reward_siswa->update($request->validated());

        return redirect()
            ->route('kesiswaan.reward.index')
            ->with('status', __('Reward diperbarui.'));
    }

    public function destroy(RewardSiswa $reward_siswa): RedirectResponse
    {
        Gate::authorize('delete', $reward_siswa);

        $reward_siswa->delete();

        return redirect()
            ->route('kesiswaan.reward.index')
            ->with('status', __('Reward dihapus.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formContext(?RewardSiswa $row = null): array
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
