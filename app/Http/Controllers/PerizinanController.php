<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerizinanRequest;
use App\Http\Requests\UpdatePerizinanRequest;
use App\Models\Kelas;
use App\Models\Perizinan;
use App\Models\Siswa;
use App\Models\User;
use App\Notifications\PerizinanStatusChanged;
use App\Notifications\PerizinanSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class PerizinanController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Perizinan::class);

        $kelasId = request('kelas_id');
        $status = request('status');
        $tanggal = request('tanggal');

        $rows = Perizinan::query()
            ->with(['siswa.kelas:id,tingkat,nama', 'diajukanOleh:id,name', 'ditinjauOleh:id,name'])
            ->when($kelasId, function ($q) use ($kelasId): void {
                $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId));
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($tanggal, fn ($q) => $q->whereDate('tanggal', $tanggal))
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

        return view('perizinan.index', compact('rows', 'filterKelasOptions', 'kelasId', 'status', 'tanggal'));
    }

    public function create(): View
    {
        Gate::authorize('create', Perizinan::class);

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasId = old('kelas_id', request('kelas_id'));
        $siswas = collect();
        if ($kelasId) {
            $siswas = Siswa::query()
                ->where('kelas_id', $kelasId)
                ->orderBy('nama')
                ->get(['id', 'nama', 'nis']);
        }

        return view('perizinan.create', compact('kelasOptions', 'kelasId', 'siswas'));
    }

    public function store(StorePerizinanRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except('kelas_id')->all();
        $data['diajukan_oleh'] = $request->user()->id;
        $this->applyReviewMetadata($request, $data);

        $perizinan = Perizinan::query()->create($data);

        if ($perizinan->status === 'pending') {
            $admins = User::query()
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'admin']))
                ->get();
            Notification::send($admins, new PerizinanSubmitted($perizinan));
        }

        return redirect()
            ->route('perizinan.index')
            ->with('status', __('Pengajuan perizinan tercatat.'));
    }

    public function edit(Perizinan $perizinan): View
    {
        Gate::authorize('update', $perizinan);

        $perizinan->load(['siswa.kelas:id,tingkat,nama']);

        $kelasId = request('kelas_id') ?: old('kelas_id');
        if (! $kelasId && $perizinan->siswa?->kelas_id) {
            $kelasId = (string) $perizinan->siswa->kelas_id;
        }

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $siswas = collect();
        if ($kelasId) {
            $siswas = Siswa::query()
                ->where('kelas_id', $kelasId)
                ->orderBy('nama')
                ->get(['id', 'nama', 'nis']);
        }

        if ($siswas->isEmpty() && $perizinan->siswa_id) {
            $siswas = Siswa::query()->whereKey($perizinan->siswa_id)->get(['id', 'nama', 'nis']);
        }

        return view('perizinan.edit', compact('perizinan', 'kelasOptions', 'kelasId', 'siswas'));
    }

    public function update(UpdatePerizinanRequest $request, Perizinan $perizinan): RedirectResponse
    {
        $oldStatus = $perizinan->status;

        $data = collect($request->validated())->except('kelas_id')->all();
        $this->applyReviewMetadata($request, $data);

        $perizinan->update($data);

        if ($oldStatus !== $perizinan->status) {
            $perizinan->loadMissing(['siswa.walis', 'diajukanOleh']);
            $recipients = collect()
                ->merge($perizinan->siswa?->walis ?? collect())
                ->when($perizinan->diajukanOleh, fn ($c) => $c->push($perizinan->diajukanOleh))
                ->unique('id')
                ->values();

            Notification::send($recipients, new PerizinanStatusChanged($perizinan, $oldStatus));
        }

        return redirect()
            ->route('perizinan.index')
            ->with('status', __('Perizinan diperbarui.'));
    }

    public function destroy(Perizinan $perizinan): RedirectResponse
    {
        Gate::authorize('delete', $perizinan);

        $perizinan->delete();

        return redirect()
            ->route('perizinan.index')
            ->with('status', __('Data perizinan dihapus.'));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function applyReviewMetadata(\Illuminate\Http\Request $request, array &$data): void
    {
        $user = $request->user();
        if (! $user?->hasAnyRole(['super_admin', 'admin'])) {
            return;
        }

        if (in_array($data['status'], ['approved', 'rejected'], true)) {
            $data['ditinjau_oleh'] = $user->id;
            $data['ditinjau_pada'] = now();
        } else {
            $data['ditinjau_oleh'] = null;
            $data['ditinjau_pada'] = null;
        }
    }
}
