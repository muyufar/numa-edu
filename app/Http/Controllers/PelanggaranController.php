<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePelanggaranRequest;
use App\Http\Requests\UpdatePelanggaranRequest;
use App\Models\BkJenisPelanggaran;
use App\Models\BkSanksi;
use App\Models\Kelas;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Services\BkMasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PelanggaranController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Pelanggaran::class);

        $kelasId = request('kelas_id');
        $siswaId = request('siswa_id');
        $tanggal = request('tanggal');

        $rows = Pelanggaran::query()
            ->with(['siswa.kelas:id,tingkat,nama,tahun_ajaran', 'dicatatOleh:id,name', 'bkJenis:id,nama', 'bkSanksi:id,nama'])
            ->when($kelasId, function ($q) use ($kelasId): void {
                $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId));
            })
            ->when($siswaId, fn ($q) => $q->where('siswa_id', $siswaId))
            ->when($tanggal, fn ($q) => $q->whereDate('tanggal', $tanggal))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $siswaFilterOptions = collect();
        if ($kelasId) {
            $siswaFilterOptions = Siswa::query()
                ->where('kelas_id', $kelasId)
                ->orderBy('nama')
                ->get(['id', 'nama', 'nis']);
        }

        $filterKelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        return view('bk.pelanggaran.index', compact(
            'rows',
            'filterKelasOptions',
            'siswaFilterOptions',
            'kelasId',
            'siswaId',
            'tanggal',
        ));
    }

    public function create(BkMasterDataService $masterData): View
    {
        Gate::authorize('create', Pelanggaran::class);

        $masterData->ensureForCurrentTenant();

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasId = old('kelas_id', request('kelas_id'));
        $siswaId = old('siswa_id', request('siswa_id'));

        if ($siswaId && ! $kelasId) {
            $siswa = Siswa::query()->find((int) $siswaId);
            if ($siswa?->kelas_id) {
                $kelasId = (string) $siswa->kelas_id;
            }
        }

        $siswas = collect();
        if ($kelasId) {
            $siswas = Siswa::query()
                ->where('kelas_id', $kelasId)
                ->orderBy('nama')
                ->get(['id', 'nama', 'nis']);
        }

        return view('bk.pelanggaran.create', compact(
            'kelasOptions',
            'kelasId',
            'siswaId',
            'siswas',
        ) + $this->masterFormOptions());
    }

    public function store(StorePelanggaranRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except('kelas_id')->all();
        $data['dicatat_oleh'] = $request->user()->id;
        $data = $this->applyJenisPelanggaran($data);

        Pelanggaran::query()->create($data);

        $siswa = Siswa::query()->find($data['siswa_id']);
        $query = array_filter([
            'kelas_id' => $siswa?->kelas_id,
            'siswa_id' => $siswa?->id,
        ], fn ($v) => $v !== null && $v !== '');

        return redirect()
            ->route('bk.pelanggaran.index', $query)
            ->with('status', __('Pelanggaran dicatat.'));
    }

    public function edit(Pelanggaran $pelanggaran, BkMasterDataService $masterData): View
    {
        Gate::authorize('update', $pelanggaran);

        $masterData->ensureForCurrentTenant();
        $pelanggaran->load(['siswa.kelas:id,tingkat,nama,tahun_ajaran', 'bkJenis', 'bkSanksi']);

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        $kelasId = $pelanggaran->siswa?->kelas_id ? (string) $pelanggaran->siswa->kelas_id : null;

        $siswas = collect();
        if ($kelasId) {
            $siswas = Siswa::query()
                ->where('kelas_id', $kelasId)
                ->orderBy('nama')
                ->get(['id', 'nama', 'nis']);
        }

        if ($siswas->isEmpty() && $pelanggaran->siswa_id) {
            $siswas = Siswa::query()
                ->whereKey($pelanggaran->siswa_id)
                ->get(['id', 'nama', 'nis']);
        }

        return view('bk.pelanggaran.edit', compact('pelanggaran', 'kelasOptions', 'kelasId', 'siswas') + $this->masterFormOptions());
    }

    public function update(UpdatePelanggaranRequest $request, Pelanggaran $pelanggaran): RedirectResponse
    {
        $data = $this->applyJenisPelanggaran($request->validated());
        $pelanggaran->update($data);

        return redirect()
            ->route('bk.pelanggaran.index')
            ->with('status', __('Pelanggaran diperbarui.'));
    }

    public function destroy(Pelanggaran $pelanggaran): RedirectResponse
    {
        Gate::authorize('delete', $pelanggaran);

        $pelanggaran->delete();

        return redirect()
            ->route('bk.pelanggaran.index')
            ->with('status', __('Catatan dihapus.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function masterFormOptions(): array
    {
        $jenisPelanggaranOptions = BkJenisPelanggaran::query()
            ->where('is_active', true)
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama', 'poin', 'tingkat']);

        $sanksiOptions = BkSanksi::query()
            ->where('is_active', true)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'nama', 'tingkat']);

        return compact('jenisPelanggaranOptions', 'sanksiOptions');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyJenisPelanggaran(array $data): array
    {
        if (empty($data['bk_jenis_pelanggaran_id'])) {
            return $data;
        }

        $jenis = BkJenisPelanggaran::query()->find($data['bk_jenis_pelanggaran_id']);
        if ($jenis) {
            $data['jenis'] = $jenis->kode;
            $data['poin'] = $data['poin'] ?? $jenis->poin;
            $data['tingkat'] = $data['tingkat'] ?? $jenis->tingkat;
        }

        return $data;
    }
}
