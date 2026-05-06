<?php

namespace App\Http\Controllers;

use App\Models\AkuntansiJurnal;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AkuntansiController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Tagihan::class);

        $recentJurnal = AkuntansiJurnal::query()
            ->with('lines.akun:id,kode,nama')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('akuntansi.index', compact('recentJurnal'));
    }
}

