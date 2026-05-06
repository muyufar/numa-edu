<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromoteSiswaFromPpdbRequest;
use App\Http\Requests\StorePpdbRegistrationRequest;
use App\Http\Requests\UpdatePpdbRegistrationRequest;
use App\Models\Kelas;
use App\Models\PpdbRegistration;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PpdbRegistrationController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', PpdbRegistration::class);

        $status = request('status');

        $registrations = PpdbRegistration::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->withExists('siswa')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('ppdb.index', compact('registrations', 'status'));
    }

    public function create(): View
    {
        Gate::authorize('create', PpdbRegistration::class);

        return view('ppdb.create');
    }

    public function store(StorePpdbRegistrationRequest $request): RedirectResponse
    {
        $registration = PpdbRegistration::query()->create(array_merge(
            $request->validated(),
            ['status' => 'submitted']
        ));

        return redirect()
            ->route('ppdb.show', $registration)
            ->with('status', __('Pendaftaran tercatat.'));
    }

    public function show(PpdbRegistration $ppdb_registration): View
    {
        Gate::authorize('view', $ppdb_registration);

        $ppdb_registration->load('siswa:id,nama,nis,ppdb_registration_id');

        $kelasOptions = collect();
        if ($ppdb_registration->status === 'accepted' && ! $ppdb_registration->siswa) {
            $kelasOptions = Kelas::query()
                ->orderByDesc('is_active')
                ->orderByDesc('tahun_ajaran')
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);
        }

        return view('ppdb.show', [
            'registration' => $ppdb_registration,
            'kelasOptions' => $kelasOptions,
        ]);
    }

    public function edit(PpdbRegistration $ppdb_registration): View
    {
        Gate::authorize('update', $ppdb_registration);

        return view('ppdb.edit', ['registration' => $ppdb_registration]);
    }

    public function update(UpdatePpdbRegistrationRequest $request, PpdbRegistration $ppdb_registration): RedirectResponse
    {
        $ppdb_registration->update($request->validated());

        return redirect()
            ->route('ppdb.show', $ppdb_registration)
            ->with('status', __('Data pendaftaran diperbarui.'));
    }

    public function destroy(PpdbRegistration $ppdb_registration): RedirectResponse
    {
        Gate::authorize('delete', $ppdb_registration);

        $ppdb_registration->delete();

        return redirect()
            ->route('ppdb.index')
            ->with('status', __('Pendaftaran dihapus.'));
    }

    public function promoteToSiswa(PromoteSiswaFromPpdbRequest $request, PpdbRegistration $ppdb_registration): RedirectResponse
    {
        $data = $request->validated();

        $siswa = DB::transaction(function () use ($ppdb_registration, $data): Siswa {
            return Siswa::query()->create([
                'user_id' => null,
                'ppdb_registration_id' => $ppdb_registration->id,
                'kelas_id' => $data['kelas_id'] ?? null,
                'nis' => $data['nis'],
                'nama' => $ppdb_registration->nama,
                'tanggal_lahir' => $ppdb_registration->tanggal_lahir,
                'jenis_kelamin' => $ppdb_registration->jenis_kelamin,
                'alamat' => $ppdb_registration->alamat,
            ]);
        });

        return redirect()
            ->route('siswa.edit', $siswa)
            ->with('status', __('Siswa berhasil dibuat dari PPDB. Lengkapi data jika perlu.'));
    }
}
