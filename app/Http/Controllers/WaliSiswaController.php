<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\Sekolah;

class WaliSiswaController extends Controller
{
    public function edit(Siswa $siswa): View
    {
        Gate::authorize('update', $siswa);

        $siswa->loadMissing('walis:id,name,email');

        $waliUsers = User::query()
            ->role('wali')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('siswa.wali', compact('siswa', 'waliUsers'));
    }

    public function store(Siswa $siswa): RedirectResponse
    {
        Gate::authorize('update', $siswa);

        $data = request()->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'hubungan' => ['required', 'string', 'max:32'],
        ]);

        $user = User::query()->findOrFail($data['user_id']);
        abort_unless($user->hasRole('wali'), 422);

        $siswa->walis()->syncWithoutDetaching([
            $user->id => ['hubungan' => $data['hubungan']],
        ]);

        return redirect()
            ->route('siswa.wali.edit', $siswa)
            ->with('status', __('Wali berhasil ditautkan.'));
    }

    public function buatAkunWali(Request $request, Siswa $siswa): RedirectResponse
    {
        Gate::authorize('update', $siswa);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'min:8'],
            'hubungan' => ['required', 'string', 'max:32'],
        ]);

        $cabangId = Sekolah::withoutGlobalScopes()
            ->whereKey($siswa->sekolah_id)
            ->value('cabang_id');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'jenis_akun' => 'wali',
            'password' => Hash::make($data['password']),
            'sekolah_id' => $siswa->sekolah_id,
            'cabang_id' => $cabangId,
        ]);

        $user->assignRole('wali');

        $siswa->walis()->syncWithoutDetaching([
            $user->id => ['hubungan' => $data['hubungan']],
        ]);

        return redirect()
            ->route('siswa.wali.edit', $siswa)
            ->with('status', __('Akun wali berhasil dibuat dan ditautkan.'));
    }

    public function destroy(Siswa $siswa, User $user): RedirectResponse
    {
        Gate::authorize('update', $siswa);

        $siswa->walis()->detach($user->id);

        return redirect()
            ->route('siswa.wali.edit', $siswa)
            ->with('status', __('Tautan wali dihapus.'));
    }
}

