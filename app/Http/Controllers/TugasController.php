<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTugasPengumpulanRequest;
use App\Http\Requests\StoreTugasRequest;
use App\Http\Requests\UpdateTugasRequest;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Tugas;
use App\Models\TugasJawabanPilihan;
use App\Models\TugasPengumpulan;
use App\Models\TugasPilihan;
use App\Services\TugasSoalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TugasController extends Controller
{
    public function __construct(
        private TugasSoalService $tugasSoalService
    ) {}

    public function index(): View
    {
        Gate::authorize('viewAny', Tugas::class);

        $query = Tugas::query()
            ->with([
                'mataPelajaran:id,kode,nama',
                'kelas:id,tingkat,nama,tahun_ajaran',
                'guru:id,nama',
                'diunggahOleh:id,name',
            ])
            ->orderByDesc('tanggal_batas')
            ->orderByDesc('id');

        $user = auth()->user();
        if ($user?->hasRole('siswa')) {
            $kelasId = $user->siswa?->kelas_id;
            $query->where('is_published', true)
                ->where(function ($w) use ($kelasId) {
                    $w->whereNull('kelas_id');
                    if ($kelasId) {
                        $w->orWhere('kelas_id', $kelasId);
                    }
                });
        }

        if ($user?->hasRole('wali')) {
            $kelasIds = $user->waliSiswas()->pluck('kelas_id')->filter()->unique();
            $query->where('is_published', true)
                ->where(function ($w) use ($kelasIds) {
                    $w->whereNull('kelas_id');
                    if ($kelasIds->isNotEmpty()) {
                        $w->orWhereIn('kelas_id', $kelasIds->all());
                    }
                });
        }

        if ($q = trim((string) request('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('judul', 'like', "%{$q}%")
                    ->orWhere('bahan_materi', 'like', "%{$q}%")
                    ->orWhere('instruksi', 'like', "%{$q}%")
                    ->orWhereHas('mataPelajaran', fn ($m) => $m->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"));
            });
        }

        if ($mapelId = request('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $mapelId);
        }

        if ($kelasId = request('kelas_id')) {
            $query->where('kelas_id', $kelasId);
        }

        if ($hari = request('hari')) {
            $query->where('hari', $hari);
        }

        if (($semester = request('semester')) !== null && $semester !== '') {
            $query->where('semester', $semester);
        }

        if (request('status') === 'aktif') {
            $query->where(function ($w) {
                $w->whereNull('tanggal_batas')
                    ->orWhereDate('tanggal_batas', '>=', now()->toDateString());
            });
        } elseif (request('status') === 'lewat') {
            $query->whereNotNull('tanggal_batas')
                ->whereDate('tanggal_batas', '<', now()->toDateString());
        }

        $items = $query->paginate(15)->withQueryString();

        $pengumpulanByTugasId = collect();
        if ($user?->hasRole('siswa') && $user->siswa) {
            $pengumpulanByTugasId = TugasPengumpulan::query()
                ->where('siswa_id', $user->siswa->id)
                ->whereIn('tugas_id', $items->pluck('id'))
                ->get()
                ->keyBy('tugas_id');
        }

        $mapelOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']);
        $kelasOptions = Kelas::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun_ajaran')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);

        return view('tugas.index', compact('items', 'mapelOptions', 'kelasOptions', 'pengumpulanByTugasId'));
    }

    public function create(): View
    {
        Gate::authorize('create', Tugas::class);

        return view('tugas.create', $this->formOptions());
    }

    public function store(StoreTugasRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $jenisSoal = (string) ($data['jenis_soal'] ?? 'esai');
        $soalPayload = $request->input('soal');

        unset($data['soal']);

        if ($jenisSoal === 'pilihan_ganda') {
            $data['bahan_materi'] = null;
        }

        $data['is_published'] = $request->boolean('is_published');
        $data['diunggah_oleh'] = auth()->id();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('tugas', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['mime'] = $file->getClientMimeType();
            $data['size'] = $file->getSize();
        }

        $tugas = Tugas::query()->create($data);

        $this->tugasSoalService->sync($tugas, $jenisSoal, is_array($soalPayload) ? $soalPayload : null);

        return redirect()
            ->route('tugas.show', $tugas)
            ->with('status', __('Tugas berhasil dibuat.'));
    }

    public function show(Tugas $tugas): View
    {
        Gate::authorize('view', $tugas);

        $tugas->load([
            'mataPelajaran:id,kode,nama',
            'kelas:id,tingkat,nama,tahun_ajaran',
            'guru:id,nama',
            'diunggahOleh:id,name',
            'soals.pilihans',
        ]);

        $pengumpulan = null;
        $user = auth()->user();
        if ($user?->hasRole('siswa') && $user->siswa) {
            $pengumpulan = TugasPengumpulan::query()
                ->with('jawabanPilihans.pilihan:id,label,teks')
                ->where('tugas_id', $tugas->id)
                ->where('siswa_id', $user->siswa->id)
                ->first();
        }

        return view('tugas.show', compact('tugas', 'pengumpulan'));
    }

    public function kerjakan(Tugas $tugas): View
    {
        Gate::authorize('submit', $tugas);

        $tugas->load([
            'mataPelajaran:id,kode,nama',
            'kelas:id,tingkat,nama,tahun_ajaran',
            'guru:id,nama',
            'soals.pilihans',
        ]);

        return view('tugas.kerjakan', compact('tugas'));
    }

    public function submitKerjakan(StoreTugasPengumpulanRequest $request, Tugas $tugas): RedirectResponse
    {
        $siswa = auth()->user()->siswa;
        abort_if(! $siswa, 403);

        $tugas->load('soals.pilihans');

        DB::transaction(function () use ($request, $tugas, $siswa): void {
            $pengumpulan = TugasPengumpulan::query()->create([
                'tugas_id' => $tugas->id,
                'siswa_id' => $siswa->id,
                'status' => 'submitted',
                'dikumpulkan_pada' => now(),
            ]);

            if ($tugas->isPilihanGanda()) {
                $benar = 0;
                $total = $tugas->soals->count();

                foreach ((array) $request->input('jawaban', []) as $soalId => $pilihanId) {
                    $pilihan = TugasPilihan::query()
                        ->whereKey((int) $pilihanId)
                        ->where('tugas_soal_id', (int) $soalId)
                        ->firstOrFail();

                    if ($pilihan->is_benar) {
                        $benar++;
                    }

                    TugasJawabanPilihan::query()->create([
                        'tugas_pengumpulan_id' => $pengumpulan->id,
                        'tugas_soal_id' => (int) $soalId,
                        'tugas_pilihan_id' => $pilihan->id,
                        'is_benar' => $pilihan->is_benar,
                    ]);
                }

                if ($total > 0 && $tugas->bobot) {
                    $pengumpulan->nilai_otomatis = (int) round(($benar / $total) * $tugas->bobot);
                } elseif ($total > 0) {
                    $pengumpulan->nilai_otomatis = (int) round(($benar / $total) * 100);
                }

                $pengumpulan->save();
            } else {
                $data = [
                    'jawaban_esai' => $request->validated('jawaban_esai'),
                ];

                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $data['file_path'] = $file->store('tugas-pengumpulan', 'public');
                    $data['file_name'] = $file->getClientOriginalName();
                    $data['mime'] = $file->getClientMimeType();
                    $data['size'] = $file->getSize();
                }

                $pengumpulan->update($data);
            }
        });

        return redirect()
            ->route('tugas.show', $tugas)
            ->with('status', __('Tugas berhasil dikumpulkan.'));
    }

    public function edit(Tugas $tugas): View
    {
        Gate::authorize('update', $tugas);

        $tugas->load(['soals.pilihans']);

        return view('tugas.edit', array_merge(['tugas' => $tugas], $this->formOptions()));
    }

    public function update(UpdateTugasRequest $request, Tugas $tugas): RedirectResponse
    {
        $data = $request->validated();
        $jenisSoal = (string) ($data['jenis_soal'] ?? 'esai');
        $soalPayload = $request->input('soal');

        unset($data['soal']);

        if ($jenisSoal === 'pilihan_ganda') {
            $data['bahan_materi'] = null;
        }

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($tugas->file_path);

            $file = $request->file('file');
            $data['file_path'] = $file->store('tugas', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['mime'] = $file->getClientMimeType();
            $data['size'] = $file->getSize();
        }

        $tugas->update($data);

        $this->tugasSoalService->sync($tugas, $jenisSoal, is_array($soalPayload) ? $soalPayload : null);

        return redirect()
            ->route('tugas.show', $tugas)
            ->with('status', __('Tugas berhasil diperbarui.'));
    }

    public function destroy(Tugas $tugas): RedirectResponse
    {
        Gate::authorize('delete', $tugas);

        Storage::disk('public')->delete($tugas->file_path);
        $tugas->delete();

        return redirect()
            ->route('tugas.index')
            ->with('status', __('Tugas dihapus.'));
    }

    public function download(Tugas $tugas)
    {
        Gate::authorize('view', $tugas);

        if (! $tugas->file_path) {
            abort(404);
        }

        return Storage::disk('public')->download($tugas->file_path, $tugas->file_name);
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'mapelOptions' => MataPelajaran::query()->orderBy('nama')->get(['id', 'kode', 'nama']),
            'kelasOptions' => Kelas::query()
                ->orderByDesc('is_active')
                ->orderByDesc('tahun_ajaran')
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']),
            'guruOptions' => Guru::query()->orderBy('nama')->get(['id', 'nama']),
        ];
    }
}
