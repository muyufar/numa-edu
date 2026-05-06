<?php

namespace App\Http\Controllers;

use App\Models\KewajibanPembayaran;
use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KewajibanPembayaranController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $q = trim((string) $request->query('q', ''));
        $tipe = (string) $request->query('tipe', '');
        $active = (string) $request->query('active', '');

        $rows = KewajibanPembayaran::query()
            ->when($q !== '', fn ($b) => $b->where('nama', 'like', '%'.$q.'%'))
            ->when($tipe !== '', fn ($b) => $b->where('tipe', $tipe))
            ->when($active !== '', fn ($b) => $b->where('is_active', $active === '1'))
            ->orderByDesc('is_active')
            ->orderBy('tipe')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('keuangan.kewajiban.index', compact('rows', 'q', 'tipe', 'active'));
    }

    public function create(): View
    {
        Gate::authorize('create', Tagihan::class);

        return view('keuangan.kewajiban.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:64'],
            'tipe' => ['required', 'string', Rule::in(KewajibanPembayaran::TIPE_OPTIONS)],
            'nominal_default' => ['required', 'numeric', 'min:0'],
            'berlaku_mulai' => ['nullable', 'string', 'max:16'],
            'batas_hari_bayar' => ['nullable', 'integer', 'min:1', 'max:28'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        KewajibanPembayaran::query()->create($data);

        return redirect()
            ->route('keuangan.kewajiban.index')
            ->with('status', __('Master kewajiban berhasil dibuat.'));
    }

    public function edit(KewajibanPembayaran $kewajiban): View
    {
        Gate::authorize('create', Tagihan::class);

        return view('keuangan.kewajiban.edit', compact('kewajiban'));
    }

    public function update(Request $request, KewajibanPembayaran $kewajiban): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:64'],
            'tipe' => ['required', 'string', Rule::in(KewajibanPembayaran::TIPE_OPTIONS)],
            'nominal_default' => ['required', 'numeric', 'min:0'],
            'berlaku_mulai' => ['nullable', 'string', 'max:16'],
            'batas_hari_bayar' => ['nullable', 'integer', 'min:1', 'max:28'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $kewajiban->update($data);

        return redirect()
            ->route('keuangan.kewajiban.index')
            ->with('status', __('Master kewajiban diperbarui.'));
    }

    public function destroy(KewajibanPembayaran $kewajiban): RedirectResponse
    {
        Gate::authorize('create', Tagihan::class);

        $kewajiban->delete();

        return redirect()
            ->route('keuangan.kewajiban.index')
            ->with('status', __('Master kewajiban dihapus.'));
    }
}

