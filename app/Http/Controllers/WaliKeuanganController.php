<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Support\WaliKeuanganSummary;
use App\Support\WaliSiswaAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaliKeuanganController extends Controller
{
    public function dashboard(Siswa $siswa): View
    {
        $this->assertWaliLinked($siswa);

        $siswa->loadMissing('kelas:id,tingkat,nama,tahun_ajaran');

        $summary = WaliKeuanganSummary::forSiswa($siswa);

        $pembayarans = Pembayaran::query()
            ->whereHas('tagihan', fn ($q) => $q->where('siswa_id', $siswa->id))
            ->with(['tagihan:id,jenis,periode,siswa_id'])
            ->orderByDesc('dibayar_pada')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('wali.keuangan.dashboard', [
            'siswa' => $siswa,
            'summary' => $summary,
            'pembayarans' => $pembayarans,
        ]);
    }

    public function index(Request $request, Siswa $siswa): View
    {
        return $this->dashboard($siswa);
    }

    public function show(Siswa $siswa, Tagihan $tagihan): View
    {
        $this->assertWaliLinked($siswa);
        abort_unless((int) $tagihan->siswa_id === (int) $siswa->id, 404);

        $tagihan->load([
            'pembayarans' => fn ($q) => $q->orderByDesc('dibayar_pada')->orderByDesc('id'),
            'pembayarans.dicatatOleh:id,name',
        ]);

        $sisa = $tagihan->sisa();

        return view('wali.tagihan.show', compact('siswa', 'tagihan', 'sisa'));
    }

    private function assertWaliLinked(Siswa $siswa): void
    {
        abort_unless(auth()->user()?->hasRole('wali'), 403);
        abort_unless(WaliSiswaAccess::canViewSiswa(auth()->user(), $siswa), 403);
    }
}
