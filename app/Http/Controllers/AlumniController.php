<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AlumniController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Siswa::class);

        $alumnis = Siswa::query()
            ->alumni()
            ->with('kelas:id,tingkat,nama,tahun_ajaran')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q')->trim()->toString().'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('nama', 'like', $term)
                        ->orWhere('nis', 'like', $term)
                        ->orWhere('nisn', 'like', $term);
                });
            })
            ->orderByDesc('updated_at')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('siswa.alumni.index', compact('alumnis'));
    }
}
