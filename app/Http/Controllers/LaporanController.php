<?php

namespace App\Http\Controllers;

use App\Models\KurikulumItem;
use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\PresensiSiswa;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Support\PeriodeBulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::user()?->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']), 403);

        $kelasOptions = collect();
        if (Auth::user()?->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang'])) {
            $kelasOptions = Kelas::query()
                ->where('is_active', true)
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get(['id', 'tingkat', 'nama', 'tahun_ajaran']);
        }

        return view('laporan.index', compact('kelasOptions'));
    }

    public function exportSiswa(): StreamedResponse
    {
        abort_unless(Auth::user()?->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']), 403);

        $filename = 'siswa-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['nis', 'nisn', 'nama', 'kelas', 'tanggal_lahir', 'jenis_kelamin', 'alamat']);

            Siswa::query()
                ->with('kelas:id,tingkat,nama,tahun_ajaran')
                ->orderBy('nama')
                ->chunk(200, function ($chunk) use ($out): void {
                    foreach ($chunk as $s) {
                        $kelas = $s->kelas;
                        $kelasLabel = $kelas ? trim("{$kelas->tingkat} {$kelas->nama} {$kelas->tahun_ajaran}") : '';

                        fputcsv($out, [
                            $s->nis,
                            $s->nisn ?? '',
                            $s->nama,
                            $kelasLabel,
                            $s->tanggal_lahir?->format('Y-m-d') ?? '',
                            $s->jenis_kelamin ?? '',
                            $s->alamat ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportNilai(): StreamedResponse
    {
        abort_unless(Auth::user()?->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']), 403);

        $filename = 'nilai-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['tahun_ajaran', 'semester', 'kelas', 'nis', 'nama_siswa', 'mapel', 'nilai_akhir']);

            Nilai::query()
                ->with([
                    'siswa:id,nis,nama',
                    'kelas:id,tingkat,nama,tahun_ajaran',
                    'mataPelajaran:id,nama',
                ])
                ->orderBy('tahun_ajaran')
                ->orderBy('semester')
                ->orderBy('kelas_id')
                ->chunk(200, function ($chunk) use ($out): void {
                    foreach ($chunk as $n) {
                        $kelas = $n->kelas;
                        $kelasLabel = $kelas ? trim("{$kelas->tingkat} {$kelas->nama} {$kelas->tahun_ajaran}") : '';

                        fputcsv($out, [
                            $n->tahun_ajaran,
                            $n->semester,
                            $kelasLabel,
                            $n->siswa?->nis ?? '',
                            $n->siswa?->nama ?? '',
                            $n->mataPelajaran?->nama ?? '',
                            (string) $n->nilai_akhir,
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPresensiSiswa(): StreamedResponse
    {
        abort_unless(Auth::user()?->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']), 403);

        $filename = 'presensi-siswa-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['tanggal', 'nis', 'nama_siswa', 'kelas', 'status', 'keterangan']);

            PresensiSiswa::query()
                ->with(['siswa.kelas:id,tingkat,nama,tahun_ajaran'])
                ->orderByDesc('tanggal')
                ->orderBy('id')
                ->chunk(300, function ($chunk) use ($out): void {
                    foreach ($chunk as $p) {
                        $s = $p->siswa;
                        $kelas = $s?->kelas;
                        $kelasLabel = $kelas ? trim("{$kelas->tingkat} {$kelas->nama} {$kelas->tahun_ajaran}") : '';

                        fputcsv($out, [
                            $p->tanggal?->format('Y-m-d') ?? '',
                            $s?->nis ?? '',
                            $s?->nama ?? '',
                            $kelasLabel,
                            $p->status,
                            $p->keterangan ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportKurikulum(): StreamedResponse
    {
        abort_unless(Auth::user()?->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']), 403);

        $filename = 'kurikulum-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'tahun_ajaran',
                'semester',
                'tingkat',
                'kode_mapel',
                'nama_mapel',
                'jam_per_minggu',
                'urutan',
                'aktif',
                'catatan',
            ]);

            KurikulumItem::query()
                ->with('mataPelajaran:id,kode,nama')
                ->orderByDesc('tahun_ajaran')
                ->orderBy('semester')
                ->orderBy('tingkat')
                ->orderBy('urutan')
                ->chunk(200, function ($chunk) use ($out): void {
                    foreach ($chunk as $row) {
                        $m = $row->mataPelajaran;
                        fputcsv($out, [
                            $row->tahun_ajaran,
                            $row->semester,
                            (string) $row->tingkat,
                            $m?->kode ?? '',
                            $m?->nama ?? '',
                            $row->jam_per_minggu !== null ? (string) $row->jam_per_minggu : '',
                            (string) $row->urutan,
                            $row->is_active ? '1' : '0',
                            $row->catatan ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportTagihan(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        $request->merge([
            'kelas_id' => $request->filled('kelas_id') ? $request->input('kelas_id') : null,
        ]);

        $filename = 'tagihan-'.now()->format('Y-m-d-His').'.csv';

        $filters = $request->validate([
            'periode_from' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'periode_to' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'status' => ['nullable', 'string', 'in:unpaid,partial,paid'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
        ]);

        if (! empty($filters['periode_from']) && ! empty($filters['periode_to'])) {
            [$filters['periode_from'], $filters['periode_to']] = PeriodeBulan::orderMonths($filters['periode_from'], $filters['periode_to']);
        }

        return response()->streamDownload(function () use ($filters): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'id',
                'nis',
                'nama_siswa',
                'kelas',
                'jenis',
                'periode',
                'jumlah',
                'total_dibayar',
                'sisa',
                'status',
                'jatuh_tempo',
            ]);

            Tagihan::query()
                ->with(['siswa:id,nis,nama,kelas_id', 'siswa.kelas:id,tingkat,nama,tahun_ajaran'])
                ->withSum('pembayarans as total_bayar', 'jumlah')
                ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
                ->when($filters['periode_from'] ?? null, fn ($q, $v) => $q->where('periode', '>=', $v))
                ->when($filters['periode_to'] ?? null, fn ($q, $v) => $q->where('periode', '<=', $v))
                ->when($filters['kelas_id'] ?? null, function ($q, $kelasId) {
                    $q->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', $kelasId));
                })
                ->orderByDesc('updated_at')
                ->chunk(200, function ($chunk) use ($out): void {
                    foreach ($chunk as $t) {
                        $total = (float) ($t->total_bayar ?? 0);
                        $jumlah = (float) $t->jumlah;
                        $sisa = max(0, $jumlah - $total);
                        $kelas = $t->siswa?->kelas;
                        $kelasLabel = $kelas ? trim("{$kelas->tingkat} {$kelas->nama} {$kelas->tahun_ajaran}") : '';
                        fputcsv($out, [
                            $t->id,
                            $t->siswa?->nis ?? '',
                            $t->siswa?->nama ?? '',
                            $kelasLabel,
                            $t->jenis,
                            $t->periode,
                            number_format($jumlah, 2, '.', ''),
                            number_format($total, 2, '.', ''),
                            number_format($sisa, 2, '.', ''),
                            $t->status,
                            $t->jatuh_tempo?->format('Y-m-d') ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPembayaran(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Tagihan::class);

        $request->merge([
            'kelas_id' => $request->filled('kelas_id') ? $request->input('kelas_id') : null,
        ]);

        $filename = 'pembayaran-'.now()->format('Y-m-d-His').'.csv';

        $filters = $request->validate([
            'periode_from' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'periode_to' => ['nullable', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'metode' => ['nullable', 'string', 'in:tunai,transfer,virtual,lainnya'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
        ]);

        if (! empty($filters['periode_from']) && ! empty($filters['periode_to'])) {
            [$filters['periode_from'], $filters['periode_to']] = PeriodeBulan::orderMonths($filters['periode_from'], $filters['periode_to']);
        }

        return response()->streamDownload(function () use ($filters): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'id',
                'tagihan_id',
                'nis',
                'nama_siswa',
                'kelas',
                'jenis_tagihan',
                'periode',
                'jumlah',
                'metode',
                'referensi',
                'dibayar_pada',
                'dicatat_oleh',
            ]);

            Pembayaran::query()
                ->with([
                    'tagihan.siswa:id,nis,nama,kelas_id',
                    'tagihan.siswa.kelas:id,tingkat,nama,tahun_ajaran',
                    'tagihan:id,siswa_id,jenis,periode',
                    'dicatatOleh:id,name',
                ])
                ->when($filters['metode'] ?? null, fn ($q, $v) => $q->where('metode', $v))
                ->when(($filters['periode_from'] ?? null) || ($filters['periode_to'] ?? null) || ($filters['kelas_id'] ?? null), function ($q) use ($filters) {
                    $q->whereHas('tagihan', function ($tq) use ($filters) {
                        if (! empty($filters['periode_from'])) {
                            $tq->where('periode', '>=', $filters['periode_from']);
                        }
                        if (! empty($filters['periode_to'])) {
                            $tq->where('periode', '<=', $filters['periode_to']);
                        }
                        if (! empty($filters['kelas_id'])) {
                            $tq->whereHas('siswa', fn ($sq) => $sq->where('kelas_id', (int) $filters['kelas_id']));
                        }
                    });
                })
                ->orderByDesc('dibayar_pada')
                ->orderByDesc('id')
                ->chunk(200, function ($chunk) use ($out): void {
                    foreach ($chunk as $p) {
                        $tg = $p->tagihan;
                        $s = $tg?->siswa;
                        $kelas = $s?->kelas;
                        $kelasLabel = $kelas ? trim("{$kelas->tingkat} {$kelas->nama} {$kelas->tahun_ajaran}") : '';
                        fputcsv($out, [
                            $p->id,
                            $p->tagihan_id,
                            $s?->nis ?? '',
                            $s?->nama ?? '',
                            $kelasLabel,
                            $tg?->jenis ?? '',
                            $tg?->periode ?? '',
                            number_format((float) $p->jumlah, 2, '.', ''),
                            $p->metode,
                            $p->referensi ?? '',
                            $p->dibayar_pada?->format('Y-m-d H:i') ?? '',
                            $p->dicatatOleh?->name ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
