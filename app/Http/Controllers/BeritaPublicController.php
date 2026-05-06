<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\View\View;

class BeritaPublicController extends Controller
{
    public function index(): View
    {
        $beritas = Berita::query()
            ->published()
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('informasi.index', compact('beritas'));
    }

    public function show(string $slug): View
    {
        $berita = Berita::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('informasi.show', compact('berita'));
    }
}
