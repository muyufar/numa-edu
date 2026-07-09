<?php

namespace App\Http\Controllers;

use App\Exports\SiswaExport;
use App\Exports\SiswaTemplateExport;
use App\Imports\SiswaImport;
use App\Http\Requests\UpdateSiswaRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Services\SiswaAkunService;
use App\Support\SiswaDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function __construct(
        private SiswaAkunService $siswaAkunService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        Gate::authorize('viewAny', Siswa::class);

        if ($request->boolean('export')) {
            $filename = 'siswa-'.now()->format('Y-m-d-His').'.xlsx';

            return Excel::download(new SiswaExport(), $filename);
        }

        if ($request->boolean('template')) {
            $filename = 'template-siswa.xlsx';

            return Excel::download(new SiswaTemplateExport(), $filename);
        }

        if ($request->boolean('import')) {
            return view('siswa.import-export', [
                'totalSiswa' => Siswa::query()->count(),
            ]);
        }

        $siswas = Siswa::query()
            ->with('kelas')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('siswa.index', compact('siswas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Siswa::class);

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        return view('siswa.create', compact('kelasOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Siswa::class);

        // Import from Excel
        if ($request->hasFile('file')) {
            $data = $request->validate([
                'file' => ['required', 'file', 'mimes:xlsx,xls'],
            ]);

            $import = new SiswaImport();

            try {
                Excel::import($import, $data['file']);
            } catch (\Throwable $e) {
                return back()->withErrors([
                    'file' => __('Gagal melakukan import: :message', ['message' => $e->getMessage()]),
                ]);
            }

            $status = __('Import siswa selesai. Diproses: :processed, dibuat: :created, diperbarui: :updated.', [
                'processed' => $import->processed,
                'created' => $import->created,
                'updated' => $import->updated,
            ]);

            return redirect()
                ->route('siswa.index', ['import' => 1])
                ->with('status', $status);
        }

        // Create via normal HTML form
        $validated = $request->validate([
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'nis' => ['required', 'string', 'max:32', 'unique:siswas,nis'],
            'nisn' => ['nullable', 'string', 'max:32', 'unique:siswas,nisn'],
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'alamat' => ['nullable', 'string'],
        ]);

        $siswa = Siswa::query()->create($validated);
        $siswa->refresh();

        $message = __('Siswa berhasil ditambahkan.');
        if ($siswa->user) {
            $message .= ' '.__('Akun login otomatis: :email (password awal: NISN).', [
                'email' => $siswa->user->email,
            ]);
        } elseif ($siswa->nisn) {
            $message .= ' '.__('Akun login belum dibuat — email :email mungkin sudah dipakai.', [
                'email' => $siswa->suggestedAkunEmail(),
            ]);
        }

        return redirect()
            ->route('siswa.index')
            ->with('status', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Siswa $siswa): RedirectResponse
    {
        return redirect()->route('siswa.edit', $siswa);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa): View
    {
        Gate::authorize('update', $siswa);

        $siswa->loadMissing('ppdbRegistration:id,nama,status', 'user:id,name,email');

        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        return view('siswa.edit', compact('siswa', 'kelasOptions'));
    }

    public function buatAkun(Request $request, Siswa $siswa): RedirectResponse
    {
        Gate::authorize('update', $siswa);

        if ($siswa->user_id) {
            return redirect()
                ->route('siswa.edit', $siswa)
                ->with('status', __('Akun siswa sudah ada.'));
        }

        $suggestedEmail = $siswa->suggestedAkunEmail();
        if (! $suggestedEmail) {
            return redirect()
                ->route('siswa.edit', $siswa)
                ->withErrors(['email' => __('Isi NISN siswa terlebih dahulu untuk membuat akun otomatis.')]);
        }

        $data = $request->validate([
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user = $this->siswaAkunService->provision($siswa, $data['password'] ?? null);

        if (! $user) {
            return redirect()
                ->route('siswa.edit', $siswa)
                ->withErrors(['email' => __('Gagal membuat akun. Pastikan NISN terisi dan email :email belum dipakai.', [
                    'email' => $suggestedEmail,
                ])]);
        }

        return redirect()
            ->route('siswa.edit', $siswa)
            ->with('status', __('Akun siswa berhasil dibuat: :email. Password awal: NISN siswa.', [
                'email' => $user->email,
            ]));
    }

    public function updateAkun(Request $request, Siswa $siswa): RedirectResponse
    {
        Gate::authorize('update', $siswa);

        $siswa->loadMissing('user');
        abort_unless($siswa->user, 404);

        $data = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($siswa->user->id)],
        ]);

        /** @var User $user */
        $user = $siswa->user;
        abort_unless($user->hasRole('siswa'), 403);

        $user->forceFill(['email' => $data['email']])->save();

        return redirect()
            ->route('siswa.edit', $siswa)
            ->with('status', __('Email akun siswa berhasil diperbarui.'));
    }

    public function resetPasswordAkun(Request $request, Siswa $siswa): RedirectResponse
    {
        Gate::authorize('update', $siswa);

        $siswa->loadMissing('user');
        abort_unless($siswa->user, 404);

        $data = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        /** @var User $user */
        $user = $siswa->user;
        abort_unless($user->hasRole('siswa'), 403);

        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        return redirect()
            ->route('siswa.edit', $siswa)
            ->with('status', __('Password akun siswa berhasil direset.'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $hadUser = (bool) $siswa->user_id;

        $siswa->update($request->validated());
        $siswa->refresh();

        $message = __('Siswa berhasil diperbarui.');
        if (! $hadUser && $siswa->user) {
            $message .= ' '.__('Akun login otomatis dibuat: :email.', ['email' => $siswa->user->email]);
        }

        return redirect()
            ->route('siswa.index')
            ->with('status', $message);
    }

    public function updateDokumen(Request $request, Siswa $siswa): RedirectResponse
    {
        Gate::authorize('update', $siswa);

        $rules = [];
        foreach (SiswaDokumen::fields() as $key => $field) {
            $rules[$key] = ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'];
        }
        $request->validate($rules);

        $updates = [];
        foreach (SiswaDokumen::fields() as $key => $field) {
            if (! $request->hasFile($key)) {
                continue;
            }

            $oldPath = $siswa->{$field['path']};
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file($key);
            $updates[$field['path']] = $file->store('siswa/dokumen', 'public');
            $updates[$field['name']] = $file->getClientOriginalName();
        }

        if ($updates !== []) {
            $siswa->update($updates);
        }

        return redirect()
            ->route('siswa.edit', $siswa)
            ->with('status', __('Dokumen profil siswa berhasil disimpan.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        Gate::authorize('delete', $siswa);

        $siswa->delete();

        return redirect()
            ->route('siswa.index')
            ->with('status', __('Siswa berhasil dihapus.'));
    }

    public function destroyAll(Request $request): RedirectResponse
    {
        Gate::authorize('deleteAny', Siswa::class);

        $data = $request->validate([
            'confirm' => ['required', 'string'],
        ]);

        if (mb_strtoupper(trim($data['confirm'])) !== 'HAPUS') {
            return back()->withErrors([
                'confirm' => __('Ketik "HAPUS" untuk konfirmasi.'),
            ]);
        }

        $deleted = 0;

        DB::transaction(function () use (&$deleted): void {
            // Chunk delete to reduce long write-lock.
            Siswa::query()
                ->select('id')
                ->orderBy('id')
                ->chunkById(500, function ($chunk) use (&$deleted): void {
                    $ids = $chunk->pluck('id')->all();
                    if (! empty($ids)) {
                        $deleted += Siswa::query()->whereKey($ids)->delete();
                    }
                });
        });

        return redirect()
            ->route('siswa.index')
            ->with('status', __('Semua siswa berhasil dihapus. Total terhapus: :n', ['n' => $deleted]));
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        Gate::authorize('deleteAny', Siswa::class);

        $data = $request->validate([
            'confirm' => ['required', 'string'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        if (mb_strtoupper(trim($data['confirm'])) !== 'HAPUS') {
            return back()->withErrors([
                'confirm' => __('Ketik "HAPUS" untuk konfirmasi.'),
            ]);
        }

        $ids = array_values(array_unique(array_map('intval', $data['ids'])));

        $deleted = 0;
        DB::transaction(function () use (&$deleted, $ids): void {
            foreach (array_chunk($ids, 500) as $chunk) {
                $deleted += Siswa::query()->whereKey($chunk)->delete();
            }
        });

        return redirect()
            ->route('siswa.index')
            ->with('status', __('Siswa terpilih berhasil dihapus. Total terhapus: :n', ['n' => $deleted]));
    }
}

