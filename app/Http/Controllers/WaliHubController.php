<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WaliHubController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->hasRole('wali'), 403);

        $siswas = auth()->user()
            ->waliSiswas()
            ->with(['kelas:id,tingkat,nama,tahun_ajaran'])
            ->orderBy('nama')
            ->get();

        return view('wali.index', compact('siswas'));
    }

    public function show(Siswa $siswa): View
    {
        abort_unless(auth()->user()?->hasRole('wali'), 403);

        $isLinked = auth()->user()
            ->waliSiswas()
            ->where('siswas.id', $siswa->id)
            ->exists();

        abort_unless($isLinked, 403);

        $siswa->loadMissing('kelas');

        $tagihanUnpaid = $siswa->tagihans()->whereIn('status', ['unpaid', 'partial'])->count();
        $izinPending = $siswa->perizinans()->where('status', 'pending')->count();
        $pelanggaranCount = $siswa->pelanggarans()->count();
        $presensi7d = $siswa->presensiSiswas()
            ->where('tanggal', '>=', now()->subDays(7)->toDateString())
            ->orderByDesc('tanggal')
            ->take(7)
            ->get(['tanggal', 'status']);

        $nilaiLatest = $siswa->nilais()
            ->with('mataPelajaran:id,nama')
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        return view('wali.show', compact(
            'siswa',
            'tagihanUnpaid',
            'izinPending',
            'pelanggaranCount',
            'presensi7d',
            'nilaiLatest'
        ));
    }
}

