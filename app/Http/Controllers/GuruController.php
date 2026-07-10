<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Models\Guru;
use App\Models\Scopes\TenantScope;
use App\Models\User;
use App\Support\GtkDetail;
use App\Support\GtkFoto;
use App\Support\GtkProfilePayload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function show(Guru $guru): View
    {
        Gate::authorize('view', $guru);

        return view('tenaga-kependidikan.show', [
            'gtk' => GtkDetail::fromGuru($guru),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Guru::class);

        return view('guru.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $storeRequest = StoreGuruRequest::createFrom($request);
        $storeRequest->setContainer(app())->setRedirector(app('redirect'));
        $storeRequest->validateResolved();

        $data = $storeRequest->validated();

        DB::transaction(function () use ($data): void {
            $sekolahId = TenantScope::effectiveSekolahId();
            if ($sekolahId === false || $sekolahId === null) {
                $sekolahId = (int) config('tenancy.default_sekolah_id', 1);
            }

            $user = User::query()->create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => $data['password'],
                'sekolah_id' => $sekolahId,
                'email_verified_at' => now(),
            ]);

            $user->assignRole('guru');

            Guru::query()->create([
                'user_id' => $user->id,
                'nama' => $data['nama'],
                'nip' => $data['nip'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);
        });

        return redirect()
            ->route('tenaga-kependidikan.index', ['tab' => 'guru'])
            ->with('status', __('Guru berhasil ditambahkan.'));
    }

    public function edit(Guru $guru): View
    {
        Gate::authorize('update', $guru);

        $guru->load('user:id,name,email');

        return view('guru.edit', compact('guru'));
    }

    public function update(UpdateGuruRequest $request, Guru $guru): RedirectResponse
    {
        $data = $request->validated();

        $userPayload = [
            'name' => $data['nama'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $userPayload['password'] = $data['password'];
        }

        $gtkAttributes = GtkProfilePayload::gtkAttributes($data);
        $gtkAttributes['nama'] = $data['nama'];
        $gtkAttributes['nip'] = $data['nip'] ?? null;
        $gtkAttributes['phone'] = $data['phone'] ?? null;
        $gtkAttributes['tugas'] = $data['tugas'] ?? null;
        $gtkAttributes['mata_pelajaran'] = $data['mata_pelajaran'] ?? null;
        $gtkAttributes['penempatan'] = $data['penempatan'] ?? null;
        $gtkAttributes['total_jtm'] = $data['total_jtm'] ?? null;

        if ($request->boolean('hapus_foto')) {
            GtkFoto::delete($guru->foto_path);
            $gtkAttributes['foto_path'] = null;
            $gtkAttributes['foto_name'] = null;
        }

        if ($request->hasFile('foto')) {
            $gtkAttributes = array_merge($gtkAttributes, GtkFoto::store($guru, $request->file('foto')));
        }

        DB::transaction(function () use ($guru, $userPayload, $gtkAttributes): void {
            $guru->user->update($userPayload);
            $guru->update($gtkAttributes);
        });

        return redirect()
            ->route('guru.show', $guru)
            ->with('status', __('Data guru berhasil diperbarui.'));
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        Gate::authorize('delete', $guru);

        if ($guru->user_id === auth()->id()) {
            return redirect()
                ->route('tenaga-kependidikan.index', ['tab' => 'guru'])
                ->with('error', __('Akun sendiri tidak dapat dihapus dari daftar guru.'));
        }

        DB::transaction(function () use ($guru): void {
            GtkFoto::delete($guru->foto_path);
            $guru->user()->delete();
        });

        return redirect()
            ->route('tenaga-kependidikan.index', ['tab' => 'guru'])
            ->with('status', __('Guru berhasil dihapus.'));
    }
}
