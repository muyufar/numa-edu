<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Services\PresensiScanService;
use App\Support\SekolahPresensiSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresensiScanController extends Controller
{
    public function __construct(
        private PresensiScanService $scanService
    ) {}

    public function show(string $type): View
    {
        PresensiScanService::authorizeType($type);

        $kelasOptions = collect();
        if ($type === 'siswa') {
            $kelasOptions = Kelas::query()
                ->orderByDesc('is_active')
                ->orderByDesc('tahun_ajaran')
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get(['id', 'tingkat', 'nama', 'tahun_ajaran', 'is_active']);
        }

        $labels = [
            'siswa' => __('Presensi siswa'),
            'guru' => __('Presensi guru'),
            'pegawai' => __('Presensi pegawai'),
        ];

        $peopleOptions = match ($type) {
            'siswa' => Siswa::query()->orderBy('nama')->get(['id', 'nama', 'presensi_kode', 'face_descriptor']),
            'guru' => Guru::query()->orderBy('nama')->get(['id', 'nama', 'presensi_kode', 'face_descriptor']),
            'pegawai' => Pegawai::query()->where('is_active', true)->orderBy('nama')->get(['id', 'nama', 'presensi_kode', 'face_descriptor']),
            default => collect(),
        };

        return view('presensi.scan', [
            'type' => $type,
            'typeLabel' => $labels[$type] ?? $type,
            'kelasOptions' => $kelasOptions,
            'peopleOptions' => $peopleOptions,
            'indexRoute' => route('presensi.'.$type.'.index'),
            'perMapel' => $type === 'siswa' && SekolahPresensiSettings::isPerMapel(),
        ]);
    }

    public function jadwalOptions(Request $request): JsonResponse
    {
        PresensiScanService::authorizeType('siswa');
        abort_unless(SekolahPresensiSettings::isPerMapel(), 404);

        $data = $request->validate([
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'tanggal' => ['nullable', 'date'],
        ]);

        $tanggal = $data['tanggal'] ?? now()->toDateString();

        $items = Jadwal::query()
            ->with('mataPelajaran:id,nama')
            ->where('kelas_id', $data['kelas_id'])
            ->where('hari', Jadwal::hariFromDate($tanggal))
            ->ordered()
            ->get()
            ->map(fn (Jadwal $j) => [
                'id' => $j->id,
                'label' => $j->labelSingkat(),
            ])
            ->values();

        return response()->json(['items' => $items]);
    }

    public function barcode(Request $request, string $type): JsonResponse
    {
        PresensiScanService::authorizeType($type);

        $data = $request->validate([
            'kode' => ['required', 'string', 'max:64'],
            'tanggal' => ['nullable', 'date'],
            'jadwal_id' => ['nullable', 'integer', 'exists:jadwals,id'],
        ]);

        $result = $this->scanService->recordBarcode(
            $type,
            $data['kode'],
            $data['tanggal'] ?? null,
            $data['jadwal_id'] ?? null
        );

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    public function face(Request $request, string $type): JsonResponse
    {
        PresensiScanService::authorizeType($type);

        $data = $request->validate([
            'descriptor' => ['required', 'array', 'size:128'],
            'descriptor.*' => ['numeric'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'tanggal' => ['nullable', 'date'],
            'jadwal_id' => ['nullable', 'integer', 'exists:jadwals,id'],
        ]);

        $result = $this->scanService->recordFace(
            $type,
            array_map('floatval', $data['descriptor']),
            $data['kelas_id'] ?? null,
            $data['tanggal'] ?? null,
            $data['jadwal_id'] ?? null
        );

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    public function enrollFace(Request $request, string $type, int $person): JsonResponse
    {
        PresensiScanService::authorizeType($type);

        $data = $request->validate([
            'descriptor' => ['required', 'array', 'size:128'],
            'descriptor.*' => ['numeric'],
        ]);

        $saved = $this->scanService->enrollFace(
            $type,
            $person,
            array_map('floatval', $data['descriptor'])
        );

        if (! $saved) {
            return response()->json([
                'ok' => false,
                'message' => __('Data tidak ditemukan.'),
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'message' => __('Data wajah berhasil disimpan.'),
        ]);
    }

    public function kartu(string $type, int $person): View
    {
        PresensiScanService::authorizeType($type);

        $record = match ($type) {
            'siswa' => Siswa::query()->with('kelas:id,tingkat,nama')->findOrFail($person),
            'guru' => Guru::query()->findOrFail($person),
            'pegawai' => Pegawai::query()->findOrFail($person),
            default => abort(404),
        };

        $subtitle = match ($type) {
            'siswa' => $record->nis.($record->kelas ? ' · '.$record->kelas->tingkat.' '.$record->kelas->nama : ''),
            'guru', 'pegawai' => $record->nip ?? $record->jabatan ?? '',
            default => '',
        };

        return view('presensi.kartu', [
            'type' => $type,
            'record' => $record,
            'subtitle' => $subtitle,
            'scanRoute' => route('presensi.scan.show', $type),
        ]);
    }
}
