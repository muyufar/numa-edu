<?php

namespace App\Http\Controllers;

use App\Models\AkuntansiAkun;
use App\Models\Tagihan;
use App\Models\User;
use App\Support\KeuanganTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AkuntansiAkunController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $q = trim((string) $request->query('q', ''));
        $tipe = (string) $request->query('tipe', '');
        $active = (string) $request->query('active', '');

        $rows = AkuntansiAkun::query()
            ->withCount('jurnalLines')
            ->when($q !== '', function ($b) use ($q): void {
                $b->where(function ($inner) use ($q): void {
                    $inner->where('kode', 'like', '%'.$q.'%')
                        ->orWhere('nama', 'like', '%'.$q.'%');
                });
            })
            ->when($tipe !== '', fn ($b) => $b->where('tipe', $tipe))
            ->when($active !== '', fn ($b) => $b->where('is_active', $active === '1'))
            ->orderBy('kode')
            ->paginate(25)
            ->withQueryString();

        return view('keuangan.coa.index', compact('rows', 'q', 'tipe', 'active'));
    }

    public function create(): View
    {
        Gate::authorize('create', Tagihan::class);

        return view('keuangan.coa.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $sekolahId = $this->resolveSekolahId($request->user());

        $data = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:32',
                Rule::unique('akuntansi_akuns', 'kode')->where('sekolah_id', $sekolahId),
            ],
            'nama' => ['required', 'string', 'max:120'],
            'tipe' => ['required', 'string', Rule::in(AkuntansiAkun::TIPE_OPTIONS)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['sekolah_id'] = $sekolahId;

        AkuntansiAkun::query()->create($data);

        return redirect()
            ->route('keuangan.coa.index')
            ->with('status', __('Akun berhasil ditambahkan.'));
    }

    public function edit(AkuntansiAkun $coa): View
    {
        Gate::authorize('create', Tagihan::class);

        $coa->loadCount('jurnalLines');

        return view('keuangan.coa.edit', compact('coa'));
    }

    public function update(Request $request, AkuntansiAkun $coa): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $sekolahId = $this->resolveSekolahId($request->user());

        if ($coa->isReservedSystemKode()) {
            $data = $request->validate([
                'nama' => ['required', 'string', 'max:120'],
                'is_active' => ['nullable', 'boolean'],
            ]);
            $data['is_active'] = (bool) ($data['is_active'] ?? false);
            $coa->update($data);

            return redirect()
                ->route('keuangan.coa.index')
                ->with('status', __('Akun bawaan diperbarui (nama & status).'));
        }

        $data = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:32',
                Rule::unique('akuntansi_akuns', 'kode')
                    ->where('sekolah_id', $sekolahId)
                    ->ignore($coa->id),
            ],
            'nama' => ['required', 'string', 'max:120'],
            'tipe' => ['required', 'string', Rule::in(AkuntansiAkun::TIPE_OPTIONS)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $coa->update($data);

        return redirect()
            ->route('keuangan.coa.index')
            ->with('status', __('Akun diperbarui.'));
    }

    public function destroy(AkuntansiAkun $coa): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        if ($coa->isReservedSystemKode()) {
            return redirect()
                ->route('keuangan.coa.index')
                ->withErrors(['coa' => __('Akun dengan kode 101, 401, atau 501 tidak boleh dihapus.')]);
        }

        if ($coa->jurnalLines()->exists()) {
            return redirect()
                ->route('keuangan.coa.index')
                ->withErrors(['coa' => __('Akun sudah dipakai di jurnal dan tidak boleh dihapus.')]);
        }

        $coa->delete();

        return redirect()
            ->route('keuangan.coa.index')
            ->with('status', __('Akun dihapus.'));
    }

    private function resolveSekolahId(User $user): int
    {
        return KeuanganTenant::sekolahId($user);
    }
}
