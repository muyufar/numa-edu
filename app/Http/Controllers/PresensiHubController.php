<?php

namespace App\Http\Controllers;

use App\Models\PresensiSiswa;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PresensiHubController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', PresensiSiswa::class);

        return view('presensi.index');
    }
}
