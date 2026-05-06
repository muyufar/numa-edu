<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Guru::class);

        $gurus = Guru::query()
            ->with('user:id,name,email')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('guru.index', compact('gurus'));
    }

    public function create(): View
    {
        Gate::authorize('create', Guru::class);

        return view('guru.create');
    }

    public function store(StoreGuruRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            $user = User::query()->create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => $data['password'],
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
            ->route('guru.index')
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

        DB::transaction(function () use ($guru, $data, $userPayload): void {
            $guru->user->update($userPayload);

            $guru->update([
                'nama' => $data['nama'],
                'nip' => $data['nip'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);
        });

        return redirect()
            ->route('guru.index')
            ->with('status', __('Guru berhasil diperbarui.'));
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        Gate::authorize('delete', $guru);

        if ($guru->user_id === auth()->id()) {
            return redirect()
                ->route('guru.index')
                ->with('error', __('Akun sendiri tidak dapat dihapus dari daftar guru.'));
        }

        DB::transaction(function () use ($guru): void {
            $guru->user()->delete();
        });

        return redirect()
            ->route('guru.index')
            ->with('status', __('Guru berhasil dihapus.'));
    }
}
