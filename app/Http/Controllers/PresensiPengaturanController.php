<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Support\SekolahPresensiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PresensiPengaturanController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        $sekolah = $this->resolveAuthorizedSekolah();

        if (! $sekolah) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['sekolah' => __('Pilih sekolah terlebih dahulu untuk mengatur presensi.')]);
        }

        return view('pengaturan.presensi', [
            'sekolah' => $sekolah,
            'modeOptions' => SekolahPresensiSettings::modeLabels(),
            'currentMode' => SekolahPresensiSettings::siswaMode($sekolah),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $sekolah = $this->resolveAuthorizedSekolah();

        if (! $sekolah) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['sekolah' => __('Pilih sekolah terlebih dahulu.')]);
        }

        $data = $request->validate([
            'presensi_siswa_mode' => ['required', 'string', 'in:'.implode(',', SekolahPresensiSettings::MODE_OPTIONS)],
        ]);

        $sekolah->forceFill([
            'presensi_siswa_mode' => $data['presensi_siswa_mode'],
        ])->save();

        return redirect()
            ->route('pengaturan.presensi.edit')
            ->with('status', __('Pengaturan presensi siswa berhasil disimpan.'));
    }

    private function resolveAuthorizedSekolah(): ?Sekolah
    {
        $sekolah = SekolahPresensiSettings::resolveSekolah();

        if (! $sekolah) {
            return null;
        }

        Gate::authorize('update', $sekolah);

        return $sekolah;
    }
}
