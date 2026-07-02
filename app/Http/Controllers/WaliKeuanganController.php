<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Support\WaliSiswaAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaliKeuanganController extends Controller
{
    public function index(Request $request, Siswa $siswa): View
    {
        $this->assertWaliLinked($siswa);

        $siswa->loadMissing('kelas:id,tingkat,nama,tahun_ajaran');

        $status = $request->query('status');

        $tagihans = $siswa->tagihans()
            ->withSum('pembayarans as total_dibayar', 'jumlah')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('periode')
            ->orderByDesc('id')
            ->get();

        $pembayarans = Pembayaran::query()
            ->whereHas('tagihan', fn ($q) => $q->where('siswa_id', $siswa->id))
            ->with(['tagihan:id,jenis,periode,siswa_id'])
            ->orderByDesc('dibayar_pada')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $stats = [
            'total_tagihan' => $tagihans->count(),
            'belum_lunas' => $tagihans->whereIn('status', ['unpaid', 'partial'])->count(),
            'total_sisa' => $tagihans->sum(fn (Tagihan $t) => $t->sisa()),
            'total_dibayar' => (float) Pembayaran::query()
                ->whereHas('tagihan', fn ($q) => $q->where('siswa_id', $siswa->id))
                ->sum('jumlah'),
        ];

        return view('wali.tagihan.index', compact('siswa', 'tagihans', 'pembayarans', 'stats', 'status'));
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
