<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WaliAdminController extends Controller
{
    private function assertCanManage(Request $request, User $user = null): array
    {
        $auth = $request->user();
        abort_unless($auth?->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang']), 403);

        $sid = null;
        if (! $auth->hasRole('super_admin')) {
            $sid = $auth->hasRole('pengurus_cabang') ? session('pengurus_sekolah_id') : $auth->sekolah_id;
            abort_unless($sid, 403);
        }

        if ($user) {
            abort_unless($user->hasRole('wali'), 404);
            if ($sid !== null) {
                abort_unless((int) $user->sekolah_id === (int) $sid, 403);
            }
        }

        return [$auth, $sid];
    }

    public function index(Request $request): View
    {
        [$auth] = $this->assertCanManage($request);

        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->role('wali')
            ->when(! $auth->hasRole('super_admin'), function (Builder $b) use ($auth) {
                $sid = $auth->hasRole('pengurus_cabang') ? session('pengurus_sekolah_id') : $auth->sekolah_id;
                abort_unless($sid, 403);

                $b->where('sekolah_id', (int) $sid);
            })
            ->when($q !== '', function (Builder $b) use ($q) {
                $b->where(function (Builder $s) use ($q) {
                    $s->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%');
                });
            })
            ->withCount('waliSiswas')
            ->orderByDesc('wali_siswas_count')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('wali-admin.index', compact('users', 'q'));
    }

    public function create(Request $request): View
    {
        [$auth] = $this->assertCanManage($request);

        $sekolahOptions = [];
        if ($auth->hasRole('super_admin')) {
            $sekolahOptions = Sekolah::withoutGlobalScopes()
                ->where('is_active', true)
                ->orderBy('nama')
                ->get(['id', 'nama', 'npsn'])
                ->all();
        }

        return view('wali-admin.create', [
            'sekolahOptions' => $sekolahOptions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$auth] = $this->assertCanManage($request);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'min:8'],
            'phone' => ['nullable', 'string', 'max:32'],
        ];
        if ($auth->hasRole('super_admin')) {
            $rules['sekolah_id'] = ['required', 'integer', 'exists:sekolahs,id'];
        }

        $data = $request->validate($rules);

        $sekolahId = null;
        if ($auth->hasRole('super_admin')) {
            $sekolahId = (int) $data['sekolah_id'];
        } elseif ($auth->hasRole('pengurus_cabang')) {
            $sekolahId = (int) session('pengurus_sekolah_id');
        } else {
            $sekolahId = (int) $auth->sekolah_id;
        }

        abort_unless($sekolahId, 403);

        $sekolah = Sekolah::withoutGlobalScopes()->whereKey($sekolahId)->firstOrFail();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'jenis_akun' => 'wali',
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'sekolah_id' => (int) $sekolah->id,
            'cabang_id' => $sekolah->cabang_id,
        ]);

        $user->assignRole('wali');

        return redirect()
            ->route('wali-admin.show', $user)
            ->with('status', __('Akun wali berhasil dibuat.'));
    }

    public function show(Request $request, User $user): View|RedirectResponse
    {
        [$auth, $sid] = $this->assertCanManage($request, $user);

        $user->load(['waliSiswas' => function ($q) {
            $q->with('kelas:id,tingkat,nama,tahun_ajaran')
                ->orderBy('nama');
        }]);

        $effectiveSekolahId = $auth->hasRole('super_admin')
            ? (int) $user->sekolah_id
            : (int) ($sid ?? $user->sekolah_id);

        $siswaOptions = Siswa::withoutGlobalScopes()
            ->where('sekolah_id', $effectiveSekolahId)
            ->orderBy('nama')
            ->get(['id', 'nis', 'nama'])
            ->map(fn (Siswa $s) => ['id' => $s->id, 'nis' => $s->nis, 'nama' => $s->nama])
            ->all();

        return view('wali-admin.show', [
            'wali' => $user,
            'siswaOptions' => $siswaOptions,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->assertCanManage($request, $user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);

        $user->update($data);

        return redirect()
            ->route('wali-admin.show', $user)
            ->with('status', __('Profil wali berhasil diperbarui.'));
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $this->assertCanManage($request, $user);

        $data = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        return redirect()
            ->route('wali-admin.show', $user)
            ->with('status', __('Password wali berhasil direset.'));
    }

    public function attachSiswa(Request $request, User $user): RedirectResponse
    {
        $this->assertCanManage($request, $user);

        $data = $request->validate([
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'hubungan' => ['required', 'string', 'max:32'],
        ]);

        $siswa = Siswa::query()->findOrFail((int) $data['siswa_id']);

        if ((int) $siswa->sekolah_id !== (int) $user->sekolah_id) {
            throw ValidationException::withMessages([
                'siswa_id' => __('Siswa harus berasal dari sekolah yang sama dengan wali.'),
            ]);
        }

        $user->waliSiswas()->syncWithoutDetaching([
            $siswa->id => ['hubungan' => $data['hubungan']],
        ]);

        return redirect()
            ->route('wali-admin.show', $user)
            ->with('status', __('Siswa berhasil ditautkan ke wali.'));
    }
}

