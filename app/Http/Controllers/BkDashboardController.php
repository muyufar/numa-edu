<?php

namespace App\Http\Controllers;

use App\Models\BkHomeVisit;
use App\Models\BkJenisPelanggaran;
use App\Models\BkPemanggilan;
use App\Models\BkSanksi;
use App\Models\Pelanggaran;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BkDashboardController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Pelanggaran::class);

        $stats = [
            'pelanggaran' => Pelanggaran::query()->count(),
            'jenis' => BkJenisPelanggaran::query()->where('is_active', true)->count(),
            'sanksi' => BkSanksi::query()->where('is_active', true)->count(),
            'pemanggilan' => BkPemanggilan::query()->where('status', 'terjadwal')->count(),
            'home_visit' => BkHomeVisit::query()->whereNull('dilaporkan_kepsek_at')->count(),
        ];

        return view('bk.dashboard', compact('stats'));
    }
}
