<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachSiswaToKelasRequest;
use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Kelas::class);

        $kelas = Kelas::query()
            ->with('waliKelas:id,nama,nip')
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('kelas.index', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Kelas::class);

        return view('kelas.create', [
            'guruOptions' => $this->guruOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKelasRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        Kelas::query()->create($data);

        return redirect()
            ->route('kelas.index')
            ->with('status', __('Kelas berhasil ditambahkan.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas): RedirectResponse
    {
        return redirect()->route('kelas.edit', $kelas);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas): View
    {
        Gate::authorize('update', $kelas);

        $siswasDalamKelas = $kelas->siswas()
            ->orderBy('nama')
            ->get(['id', 'nis', 'nama', 'jenis_kelamin']);

        $siswaTanpaKelas = Siswa::query()
            ->where('sekolah_id', $kelas->sekolah_id)
            ->whereNull('kelas_id')
            ->orderBy('nama')
            ->get(['id', 'nis', 'nama']);

        return view('kelas.edit', [
            'kelas' => $kelas,
            'siswasDalamKelas' => $siswasDalamKelas,
            'siswaTanpaKelas' => $siswaTanpaKelas,
            'guruOptions' => $this->guruOptions(),
        ]);
    }

    public function attachSiswa(AttachSiswaToKelasRequest $request, Kelas $kelas): RedirectResponse
    {
        Gate::authorize('update', $kelas);

        $ids = $request->validated('siswa_ids');
        $count = 0;

        foreach ($ids as $id) {
            $siswa = Siswa::query()->findOrFail((int) $id);
            Gate::authorize('update', $siswa);
            $siswa->update(['kelas_id' => $kelas->id]);
            $count++;
        }

        return redirect()
            ->route('kelas.edit', $kelas)
            ->with('status_siswa', __(':count siswa dimasukkan ke kelas ini.', ['count' => $count]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $kelas->update($data);

        return redirect()
            ->route('kelas.index')
            ->with('status', __('Kelas berhasil diperbarui.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas): RedirectResponse
    {
        Gate::authorize('delete', $kelas);

        $kelas->delete();

        return redirect()
            ->route('kelas.index')
            ->with('status', __('Kelas berhasil dihapus.'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Guru>
     */
    private function guruOptions()
    {
        return Guru::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip']);
    }
}
