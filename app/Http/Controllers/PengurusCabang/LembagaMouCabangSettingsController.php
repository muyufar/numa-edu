<?php

namespace App\Http\Controllers\PengurusCabang;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCabangMouSettingsRequest;
use App\Models\Cabang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LembagaMouCabangSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $cabang = $this->resolveCabang($request);
        $cabangs = Cabang::query()->orderBy('nama')->get();

        return view('pengurus.lembaga-mou-settings', [
            'cabang' => $cabang,
            'cabangs' => $cabangs,
        ]);
    }

    public function update(UpdateCabangMouSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $cabangId = $user->hasRole('super_admin')
            ? (int) ($validated['cabang_id'] ?? 1)
            : (int) $user->cabang_id;

        $cabang = Cabang::query()->findOrFail($cabangId);

        $stampPath = $cabang->mou_stempel_path;
        if ($request->hasFile('mou_stempel')) {
            if (is_string($stampPath) && $stampPath !== '') {
                Storage::disk('public')->delete($stampPath);
            }

            $stampPath = $request->file('mou_stempel')->store('cabang-stempel/'.$cabang->id, 'public');
        }

        $ttdPath = $cabang->mou_penandatangan_ttd_path;
        if ($request->hasFile('mou_penandatangan_ttd')) {
            if (is_string($ttdPath) && $ttdPath !== '') {
                Storage::disk('public')->delete($ttdPath);
            }

            $ttdPath = $request->file('mou_penandatangan_ttd')->store('cabang-ttd/'.$cabang->id, 'public');
        }

        $cabang->forceFill([
            'mou_lp_next_sequence' => $validated['mou_lp_next_sequence'],
            'mou_lp_number_digits' => $validated['mou_lp_number_digits'],
            'mou_lp_number_suffix' => $validated['mou_lp_number_suffix'],
            'mou_penandatangan_nama' => $validated['mou_penandatangan_nama'] ?? null,
            'mou_penandatangan_jabatan' => $validated['mou_penandatangan_jabatan'] ?? null,
            'mou_surat_kota' => $validated['mou_surat_kota'] ?? null,
            'mou_stempel_path' => $stampPath,
            'mou_penandatangan_ttd_path' => $ttdPath,
        ])->save();

        $query = $user->hasRole('super_admin') ? ['cabang_id' => $cabang->id] : [];

        return redirect()
            ->route('pengurus.lembaga-mou-settings.edit', $query)
            ->with('status', __('Pengaturan nomor MoU, stempel, dan tanda tangan cabang disimpan.'));
    }

    private function resolveCabang(Request $request): Cabang
    {
        $user = $request->user();

        if ($user->hasRole('super_admin')) {
            $id = (int) ($request->query('cabang_id') ?? $request->input('cabang_id', 1));

            return Cabang::query()->findOrFail($id);
        }

        if ($user->hasRole('pengurus_cabang') && $user->cabang_id) {
            return Cabang::query()->findOrFail($user->cabang_id);
        }

        abort(403);
    }
}
