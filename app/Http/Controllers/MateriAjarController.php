<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMateriAjarRequest;
use App\Http\Requests\UpdateMateriAjarRequest;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\MateriAjar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Support\PerangkatAjarJenis;

class MateriAjarController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', MateriAjar::class);

        $user = auth()->user();
        $tab = (string) request('tab', 'semua');

        $baseQuery = MateriAjar::query();

        if ($user?->hasRole('siswa') || $user?->hasRole('wali')) {
            $baseQuery->untukSiswaWali();
        }

        $stats = [
            'rencana' => (clone $baseQuery)->where('status_penggunaan', 'rencana')->count(),
            'aktif' => (clone $baseQuery)->where('status_penggunaan', 'aktif')->count(),
            'selesai' => (clone $baseQuery)->where('status_penggunaan', 'selesai')->count(),
            'arsip' => (clone $baseQuery)->where('status_publikasi', 'diarsipkan')->count(),
            'draft' => (clone $baseQuery)->where('status_publikasi', 'draft')->count(),
        ];

        $query = MateriAjar::query()
            ->with([
                'mataPelajaran:id,kode,nama',
                'kelas:id,tingkat,nama,tahun_ajaran',
                'guru:id,nama',
                'diunggahOleh:id,name',
            ])
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($user?->hasRole('siswa')) {
            $kelasId = $user->siswa?->kelas_id;
            $query->untukSiswaWali()->where(function ($w) use ($kelasId) {
                $w->whereNull('kelas_id');
                if ($kelasId) {
                    $w->orWhere('kelas_id', $kelasId);
                }
            });
        } elseif ($user?->hasRole('wali')) {
            $kelasIds = $user->waliSiswas()->pluck('kelas_id')->filter()->unique();
            $query->untukSiswaWali()->where(function ($w) use ($kelasIds) {
                $w->whereNull('kelas_id');
                if ($kelasIds->isNotEmpty()) {
                    $w->orWhereIn('kelas_id', $kelasIds->all());
                }
            });
        } else {
            match ($tab) {
                'rencana' => $query->where('status_penggunaan', 'rencana'),
                'aktif' => $query->where('status_penggunaan', 'aktif'),
                'selesai' => $query->where('status_penggunaan', 'selesai'),
                'arsip' => $query->where('status_publikasi', 'diarsipkan'),
                'draft' => $query->where('status_publikasi', 'draft'),
                default => null,
            };
        }

        if ($q = trim((string) request('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('judul', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhereHas('mataPelajaran', fn ($m) => $m->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"))
                    ->orWhereHas('guru', fn ($g) => $g->where('nama', 'like', "%{$q}%"));
            });
        }

        if ($mapelId = request('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $mapelId);
        }

        if ($jenis = request('jenis')) {
            $query->where('jenis', $jenis);
        }

        if (($semester = request('semester')) !== null && $semester !== '') {
            $query->where('semester', $semester);
        }

        if ($tahunAjaran = request('tahun_ajaran')) {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        if ($guruId = request('guru_id')) {
            $query->where('guru_id', $guruId);
        }

        $items = $query->paginate(15)->withQueryString();

        $mapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);
        $guruOptions = Guru::query()->orderBy('nama')->get(['id', 'nama']);
        $tahunAjaranOptions = MateriAjar::query()
            ->select('tahun_ajaran')
            ->whereNotNull('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        $arsipGroups = collect();
        if ($tab === 'arsip' && ! $user?->hasAnyRole(['siswa', 'wali'])) {
            $arsipGroups = MateriAjar::query()
                ->with(['mataPelajaran:id,kode,nama', 'guru:id,nama'])
                ->where('status_publikasi', 'diarsipkan')
                ->orderByDesc('tahun_ajaran')
                ->orderBy('mata_pelajaran_id')
                ->orderByDesc('diarsipkan_pada')
                ->get()
                ->groupBy(fn (MateriAjar $m) => ($m->tahun_ajaran ?: __('Tanpa tahun')).' · '.($m->mataPelajaran?->nama ?: __('Tanpa mapel')));
        }

        return view('materi.index', compact(
            'items',
            'mapelOptions',
            'guruOptions',
            'tahunAjaranOptions',
            'stats',
            'tab',
            'arsipGroups',
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', MateriAjar::class);

        return view('materi.create', $this->formOptions());
    }

    public function store(StoreMateriAjarRequest $request): RedirectResponse
    {
        $data = $this->prepareMateriData($request->validated());

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('perangkat-ajar', 'public');

            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['mime'] = $file->getClientMimeType();
            $data['size'] = $file->getSize();
        } else {
            $data['file_path'] = null;
            $data['file_name'] = null;
            $data['mime'] = null;
            $data['size'] = null;
        }

        $data['diunggah_oleh'] = auth()->id();
        $data['status_publikasi'] = 'draft';

        if (empty($data['guru_id']) && auth()->user()?->guru?->id) {
            $data['guru_id'] = auth()->user()->guru->id;
        }

        $materi = MateriAjar::query()->create($data);

        return redirect()
            ->route('materi.show', $materi)
            ->with('status', __('Perangkat ajar disimpan sebagai draft. Publikasikan saat siap digunakan.'));
    }

    public function show(MateriAjar $materi_ajar): View
    {
        Gate::authorize('view', $materi_ajar);

        $materi_ajar->load([
            'mataPelajaran:id,kode,nama',
            'kelas:id,tingkat,nama,tahun_ajaran',
            'guru:id,nama',
            'diunggahOleh:id,name',
            'sekolah:id,nama',
        ]);

        return view('materi.show', ['materi_ajar' => $materi_ajar]);
    }

    public function edit(MateriAjar $materi_ajar): View
    {
        Gate::authorize('update', $materi_ajar);

        return view('materi.edit', array_merge(
            ['materi_ajar' => $materi_ajar],
            $this->formOptions(),
        ));
    }

    public function update(UpdateMateriAjarRequest $request, MateriAjar $materi_ajar): RedirectResponse
    {
        $data = $this->prepareMateriData($request->validated());

        if ($request->hasFile('file')) {
            if ($materi_ajar->file_path) {
                Storage::disk('public')->delete($materi_ajar->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('perangkat-ajar', 'public');

            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['mime'] = $file->getClientMimeType();
            $data['size'] = $file->getSize();
        }

        $materi_ajar->update($data);

        return redirect()
            ->route('materi.show', $materi_ajar)
            ->with('status', __('Perangkat ajar berhasil diperbarui.'));
    }

    public function destroy(MateriAjar $materi_ajar): RedirectResponse
    {
        Gate::authorize('delete', $materi_ajar);

        Storage::disk('public')->delete($materi_ajar->file_path);
        $materi_ajar->delete();

        return redirect()
            ->route('materi.index')
            ->with('status', __('Perangkat ajar dihapus.'));
    }

    public function publish(MateriAjar $materi_ajar): RedirectResponse
    {
        Gate::authorize('publish', $materi_ajar);

        $materi_ajar->publish();

        return redirect()
            ->route('materi.show', $materi_ajar)
            ->with('status', __('Perangkat ajar dipublikasi. Siswa dan wali dapat mengunduh jika sesuai kelas.'));
    }

    public function archive(MateriAjar $materi_ajar): RedirectResponse
    {
        Gate::authorize('archive', $materi_ajar);

        $materi_ajar->archive();

        return redirect()
            ->route('materi.index', ['tab' => 'arsip'])
            ->with('status', __('Perangkat ajar diarsipkan.'));
    }

    public function updatePenggunaan(Request $request, MateriAjar $materi_ajar): RedirectResponse
    {
        Gate::authorize('update', $materi_ajar);

        $data = $request->validate([
            'status_penggunaan' => ['required', Rule::in(MateriAjar::STATUS_PENGGUNAAN_OPTIONS)],
        ]);

        $materi_ajar->update($data);

        return redirect()
            ->route('materi.show', $materi_ajar)
            ->with('status', __('Status penggunaan diperbarui.'));
    }

    public function download(MateriAjar $materi_ajar)
    {
        Gate::authorize('view', $materi_ajar);

        if (! $materi_ajar->file_path || ! Storage::disk('public')->exists($materi_ajar->file_path)) {
            abort(404, __('Berkas tidak tersedia.'));
        }

        return Storage::disk('public')->download($materi_ajar->file_path, $materi_ajar->file_name);
    }

    public function preview(MateriAjar $materi_ajar)
    {
        Gate::authorize('view', $materi_ajar);

        if (! $materi_ajar->isPdf()) {
            abort(404, __('Pratinjau hanya tersedia untuk berkas PDF.'));
        }

        if (! $materi_ajar->file_path || ! Storage::disk('public')->exists($materi_ajar->file_path)) {
            abort(404, __('Berkas tidak ditemukan.'));
        }

        return response()->file(Storage::disk('public')->path($materi_ajar->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $materi_ajar->file_name).'"',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareMateriData(array $data): array
    {
        $jenis = $data['jenis'] ?? null;
        $kontenInput = $data['konten_modul'] ?? [];
        $lkpdSistematika = $data['lkpd_sistematika'] ?? null;
        unset($data['konten_modul'], $data['lkpd_sistematika']);

        if ($jenis === 'lkpd' && $lkpdSistematika) {
            $kontenInput[\App\Support\LkpdSistematika::META_SISTEMATIKA] = $lkpdSistematika;
        }

        $konten = PerangkatAjarJenis::normalizeKonten($jenis, $kontenInput);
        $data['konten_modul'] = PerangkatAjarJenis::hasIsi($jenis, $konten) ? $konten : null;

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'mapelOptions' => MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']),
            'kelasOptions' => Kelas::query()
                ->orderByDesc('is_active')
                ->orderByDesc('tahun_ajaran')
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']),
            'guruOptions' => Guru::query()->orderBy('nama')->get(['id', 'nama']),
        ];
    }
}
