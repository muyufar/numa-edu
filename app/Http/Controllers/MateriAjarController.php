<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMateriAjarRequest;
use App\Http\Requests\UpdateMateriAjarRequest;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\MateriAjar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MateriAjarController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', MateriAjar::class);

        $query = MateriAjar::query()
            ->with([
                'mataPelajaran:id,kode,nama',
                'kelas:id,tingkat,nama,tahun_ajaran',
                'guru:id,nama',
                'diunggahOleh:id,name',
            ])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        // Visibility for siswa/wali
        $user = auth()->user();
        if ($user?->hasRole('siswa')) {
            $kelasId = $user->siswa?->kelas_id;
            $query->where(function ($w) use ($kelasId) {
                $w->whereNull('kelas_id');
                if ($kelasId) {
                    $w->orWhere('kelas_id', $kelasId);
                }
            });
        }

        if ($user?->hasRole('wali')) {
            $kelasIds = $user->waliSiswas()->pluck('kelas_id')->filter()->unique();
            $query->where(function ($w) use ($kelasIds) {
                $w->whereNull('kelas_id');
                if ($kelasIds->isNotEmpty()) {
                    $w->orWhereIn('kelas_id', $kelasIds->all());
                }
            });
        }

        if ($q = trim((string) request('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('judul', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhereHas('mataPelajaran', fn ($m) => $m->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"));
            });
        }

        if ($mapelId = request('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $mapelId);
        }

        if (($semester = request('semester')) !== null && $semester !== '') {
            $query->where('semester', $semester);
        }

        $items = $query->paginate(15)->withQueryString();

        $mapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);

        return view('materi.index', compact('items', 'mapelOptions'));
    }

    public function create(): View
    {
        Gate::authorize('create', MateriAjar::class);

        $mapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);
        $kelasOptions = Kelas::query()->orderByDesc('is_active')->orderByDesc('tahun_ajaran')->orderBy('tingkat')->orderBy('nama')->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);
        $guruOptions = Guru::query()->orderBy('nama')->get(['id', 'nama']);

        return view('materi.create', compact('mapelOptions', 'kelasOptions', 'guruOptions'));
    }

    public function store(StoreMateriAjarRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $file = $request->file('file');
        $path = $file->store('materi', 'public');

        $data['file_path'] = $path;
        $data['file_name'] = $file->getClientOriginalName();
        $data['mime'] = $file->getClientMimeType();
        $data['size'] = $file->getSize();
        $data['diunggah_oleh'] = auth()->id();

        MateriAjar::query()->create($data);

        return redirect()
            ->route('materi.index')
            ->with('status', __('Materi berhasil diunggah.'));
    }

    public function edit(MateriAjar $materi_ajar): View
    {
        Gate::authorize('update', $materi_ajar);

        $mapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);
        $kelasOptions = Kelas::query()->orderByDesc('is_active')->orderByDesc('tahun_ajaran')->orderBy('tingkat')->orderBy('nama')->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);
        $guruOptions = Guru::query()->orderBy('nama')->get(['id', 'nama']);

        return view('materi.edit', compact('materi_ajar', 'mapelOptions', 'kelasOptions', 'guruOptions'));
    }

    public function update(UpdateMateriAjarRequest $request, MateriAjar $materi_ajar): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($materi_ajar->file_path);

            $file = $request->file('file');
            $path = $file->store('materi', 'public');

            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['mime'] = $file->getClientMimeType();
            $data['size'] = $file->getSize();
        }

        $materi_ajar->update($data);

        return redirect()
            ->route('materi.index')
            ->with('status', __('Materi berhasil diperbarui.'));
    }

    public function destroy(MateriAjar $materi_ajar): RedirectResponse
    {
        Gate::authorize('delete', $materi_ajar);

        Storage::disk('public')->delete($materi_ajar->file_path);
        $materi_ajar->delete();

        return redirect()
            ->route('materi.index')
            ->with('status', __('Materi dihapus.'));
    }

    public function download(MateriAjar $materi_ajar)
    {
        Gate::authorize('view', $materi_ajar);

        return Storage::disk('public')->download($materi_ajar->file_path, $materi_ajar->file_name);
    }
}

