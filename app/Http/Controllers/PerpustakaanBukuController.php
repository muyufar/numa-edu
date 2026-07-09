<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerpustakaanBukuRequest;
use App\Http\Requests\UpdatePerpustakaanBukuRequest;
use App\Models\PerpustakaanBuku;
use App\Models\PerpustakaanKategori;
use App\Models\PerpustakaanPeminjaman;
use App\Services\PerpustakaanPeminjamanService;
use App\Support\PolicyRoles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PerpustakaanBukuController extends Controller
{
    public function __construct(
        private readonly PerpustakaanPeminjamanService $peminjamanService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', PerpustakaanBuku::class);

        $q = trim((string) $request->query('q', ''));
        $tipe = $request->query('tipe');
        $kategoriId = $request->query('kategori_id');

        $bukus = PerpustakaanBuku::query()
            ->with('kategori:id,nama')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('judul', 'like', "%{$q}%")
                        ->orWhere('pengarang', 'like', "%{$q}%")
                        ->orWhere('isbn', 'like', "%{$q}%");
                });
            })
            ->when($tipe, fn ($query) => $query->where('tipe', $tipe))
            ->when($kategoriId, fn ($query) => $query->where('perpustakaan_kategori_id', $kategoriId))
            ->orderBy('judul')
            ->paginate(18)
            ->withQueryString();

        $kategoriOptions = PerpustakaanKategori::query()->orderBy('nama')->get(['id', 'nama']);

        return view('perpustakaan.buku.index', compact('bukus', 'kategoriOptions', 'q', 'tipe', 'kategoriId'));
    }

    public function create(): View
    {
        Gate::authorize('create', PerpustakaanBuku::class);

        $buku = new PerpustakaanBuku([
            'tipe' => 'fisik',
            'jumlah_eksemplar' => 1,
            'eksemplar_tersedia' => 1,
            'bahasa' => 'id',
            'is_active' => true,
        ]);

        $kategoriOptions = PerpustakaanKategori::query()->orderBy('nama')->get();

        return view('perpustakaan.buku.create', compact('buku', 'kategoriOptions'));
    }

    public function store(StorePerpustakaanBukuRequest $request): RedirectResponse
    {
        $data = $this->prepareData($request->validated());
        $data['eksemplar_tersedia'] = $data['jumlah_eksemplar'] ?? 1;

        $buku = PerpustakaanBuku::query()->create($data);
        $this->handleUploads($request, $buku);

        return redirect()
            ->route('perpustakaan.buku.show', $buku)
            ->with('status', __('Buku berhasil ditambahkan.'));
    }

    public function show(PerpustakaanBuku $perpustakaan_buku): View
    {
        Gate::authorize('view', $perpustakaan_buku);

        $buku = $perpustakaan_buku->load('kategori');
        $user = auth()->user();

        $pinjamanAktif = null;
        if ($user) {
            $pinjamanAktif = PerpustakaanPeminjaman::query()
                ->where('perpustakaan_buku_id', $buku->id)
                ->where('user_id', $user->id)
                ->aktif()
                ->latest('id')
                ->first();
        }

        $canPreview = $user?->can('preview', $buku) ?? false;
        $canPinjam = $user?->can('pinjam', $buku) ?? false;

        return view('perpustakaan.buku.show', compact('buku', 'pinjamanAktif', 'canPreview', 'canPinjam'));
    }

    public function edit(PerpustakaanBuku $perpustakaan_buku): View
    {
        Gate::authorize('update', $perpustakaan_buku);

        $buku = $perpustakaan_buku;
        $kategoriOptions = PerpustakaanKategori::query()->orderBy('nama')->get();

        return view('perpustakaan.buku.edit', compact('buku', 'kategoriOptions'));
    }

    public function update(UpdatePerpustakaanBukuRequest $request, PerpustakaanBuku $perpustakaan_buku): RedirectResponse
    {
        $data = $this->prepareData($request->validated());
        $lamaJumlah = (int) $perpustakaan_buku->jumlah_eksemplar;
        $baruJumlah = (int) ($data['jumlah_eksemplar'] ?? $lamaJumlah);
        $dipinjam = max(0, $lamaJumlah - (int) $perpustakaan_buku->eksemplar_tersedia);
        $data['eksemplar_tersedia'] = max(0, $baruJumlah - $dipinjam);

        $perpustakaan_buku->update($data);
        $this->handleUploads($request, $perpustakaan_buku);

        return redirect()
            ->route('perpustakaan.buku.show', $perpustakaan_buku)
            ->with('status', __('Buku berhasil diperbarui.'));
    }

    public function destroy(PerpustakaanBuku $perpustakaan_buku): RedirectResponse
    {
        Gate::authorize('delete', $perpustakaan_buku);

        if ($perpustakaan_buku->peminjamans()->aktif()->exists()) {
            return back()->withErrors(['buku' => __('Tidak dapat menghapus buku yang masih dipinjam.')]);
        }

        Storage::disk('public')->delete([$perpustakaan_buku->file_path, $perpustakaan_buku->cover_path]);
        $perpustakaan_buku->delete();

        return redirect()
            ->route('perpustakaan.buku.index')
            ->with('status', __('Buku dihapus.'));
    }

    public function preview(PerpustakaanBuku $perpustakaan_buku)
    {
        Gate::authorize('preview', $perpustakaan_buku);

        if (! $perpustakaan_buku->isPdf()) {
            abort(404, __('Pratinjau hanya tersedia untuk PDF.'));
        }

        if (! $perpustakaan_buku->file_path || ! Storage::disk('public')->exists($perpustakaan_buku->file_path)) {
            abort(404, __('Berkas tidak ditemukan.'));
        }

        return response()->file(Storage::disk('public')->path($perpustakaan_buku->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $perpustakaan_buku->file_name).'"',
        ]);
    }

    public function cover(PerpustakaanBuku $perpustakaan_buku)
    {
        Gate::authorize('view', $perpustakaan_buku);

        if (! $perpustakaan_buku->hasCover()) {
            abort(404);
        }

        $path = Storage::disk('public')->path($perpustakaan_buku->cover_path);
        $mime = Storage::disk('public')->mimeType($perpustakaan_buku->cover_path) ?: 'image/jpeg';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    public function pinjam(Request $request, PerpustakaanBuku $perpustakaan_buku): RedirectResponse
    {
        Gate::authorize('pinjam', $perpustakaan_buku);

        $data = $request->validate([
            'tipe_peminjaman' => ['required', 'in:fisik,digital'],
        ]);

        $peminjam = auth()->user();
        if (PolicyRoles::perpusTim(auth()->user()) && $request->filled('user_id')) {
            $peminjam = \App\Models\User::query()->findOrFail($request->integer('user_id'));
        }

        $this->peminjamanService->pinjam(
            $peminjam,
            $perpustakaan_buku,
            $data['tipe_peminjaman'],
            auth()->user(),
        );

        return redirect()
            ->route('perpustakaan.buku.show', $perpustakaan_buku)
            ->with('status', __('Peminjaman berhasil dicatat.'));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareData(array $data): array
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        unset($data['cover'], $data['file']);

        if (! in_array($data['tipe'] ?? '', ['fisik', 'fisik_digital'], true)) {
            $data['jumlah_eksemplar'] = 0;
            $data['eksemplar_tersedia'] = 0;
        } else {
            $data['jumlah_eksemplar'] = (int) ($data['jumlah_eksemplar'] ?? 1);
        }

        return $data;
    }

    private function handleUploads(Request $request, PerpustakaanBuku $buku): void
    {
        $updates = [];

        if ($request->hasFile('cover')) {
            if ($buku->cover_path) {
                Storage::disk('public')->delete($buku->cover_path);
            }
            $cover = $request->file('cover');
            $updates['cover_path'] = $cover->store('perpustakaan/covers', 'public');
            $updates['cover_name'] = $cover->getClientOriginalName();
        }

        if ($request->hasFile('file')) {
            if ($buku->file_path) {
                Storage::disk('public')->delete($buku->file_path);
            }
            $file = $request->file('file');
            $updates['file_path'] = $file->store('perpustakaan/ebooks', 'public');
            $updates['file_name'] = $file->getClientOriginalName();
            $updates['mime'] = $file->getClientMimeType();
            $updates['size'] = $file->getSize();
        }

        if ($updates !== []) {
            $buku->update($updates);
        }
    }
}
