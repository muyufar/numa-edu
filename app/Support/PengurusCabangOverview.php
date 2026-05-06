<?php

namespace App\Support;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Support\Collection;

final class PengurusCabangOverview
{
    /**
     * @return array{
     *   cabang: Cabang|null,
     *   total_lembaga: int,
     *   total_siswa: int,
     *   total_guru: int,
     *   siswa_l: int,
     *   siswa_p: int,
     *   guru_l: int,
     *   guru_p: int,
     *   chart_kecamatan: list<array{kecamatan: string, siswa: int, guru: int}>,
     *   akreditasi: list<array{label: string, count: int, color: string}>,
     *   rekap_rows: list<array{kecamatan: string, jml_lembaga: int, desa: string}>,
     * }
     */
    public static function build(?int $cabangId): array
    {
        $cabang = $cabangId ? Cabang::query()->find($cabangId) : null;

        $sekolahQuery = Sekolah::query()->where('is_active', true);
        if ($cabangId !== null) {
            $sekolahQuery->where('cabang_id', $cabangId);
        }
        $sekolahIds = $sekolahQuery->pluck('id')->all();
        $totalLembaga = count($sekolahIds);

        if ($totalLembaga === 0) {
            return self::emptyPayload($cabang);
        }

        $unkKec = __('Belum mengisi kecamatan');
        $unkAkre = __('Belum');

        $siswaAgg = Siswa::withoutGlobalScopes()
            ->whereIn('sekolah_id', $sekolahIds)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN jenis_kelamin = 'L' THEN 1 ELSE 0 END) as l")
            ->selectRaw("SUM(CASE WHEN jenis_kelamin = 'P' THEN 1 ELSE 0 END) as p")
            ->first();

        $guruAgg = Guru::withoutGlobalScopes()
            ->whereIn('sekolah_id', $sekolahIds)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN jenis_kelamin = 'L' THEN 1 ELSE 0 END) as l")
            ->selectRaw("SUM(CASE WHEN jenis_kelamin = 'P' THEN 1 ELSE 0 END) as p")
            ->first();

        $totalSiswa = (int) ($siswaAgg->total ?? 0);
        $totalGuru = (int) ($guruAgg->total ?? 0);
        $siswaL = (int) ($siswaAgg->l ?? 0);
        $siswaP = (int) ($siswaAgg->p ?? 0);
        $guruL = (int) ($guruAgg->l ?? 0);
        $guruP = (int) ($guruAgg->p ?? 0);

        $siswaPerKec = Siswa::withoutGlobalScopes()
            ->join('sekolahs', 'sekolahs.id', '=', 'siswas.sekolah_id')
            ->whereIn('siswas.sekolah_id', $sekolahIds)
            ->selectRaw(
                'COALESCE(NULLIF(TRIM(sekolahs.nama_kecamatan), ""), ?) as kecamatan',
                [$unkKec]
            )
            ->selectRaw('COUNT(*) as c')
            ->groupBy('kecamatan')
            ->pluck('c', 'kecamatan');

        $guruPerKec = Guru::withoutGlobalScopes()
            ->join('sekolahs', 'sekolahs.id', '=', 'gurus.sekolah_id')
            ->whereIn('gurus.sekolah_id', $sekolahIds)
            ->selectRaw(
                'COALESCE(NULLIF(TRIM(sekolahs.nama_kecamatan), ""), ?) as kecamatan',
                [$unkKec]
            )
            ->selectRaw('COUNT(*) as c')
            ->groupBy('kecamatan')
            ->pluck('c', 'kecamatan');

        $kecamatanKeys = $siswaPerKec->keys()->merge($guruPerKec->keys())->unique()->values();

        $chartKec = $kecamatanKeys->map(function (string $kec) use ($siswaPerKec, $guruPerKec) {
            return [
                'kecamatan' => $kec,
                'siswa' => (int) $siswaPerKec->get($kec, 0),
                'guru' => (int) $guruPerKec->get($kec, 0),
            ];
        })->sortByDesc(fn (array $r) => $r['siswa'] + $r['guru'])->values()->all();

        $akreColors = [
            'A' => '#22c55e',
            'B' => '#3b82f6',
            'C' => '#f59e0b',
            'Belum' => '#94a3b8',
        ];

        $akreRows = Sekolah::query()
            ->whereIn('id', $sekolahIds)
            ->pluck('akreditasi')
            ->groupBy(function (?string $a) use ($unkAkre) {
                $x = strtoupper(trim((string) $a));

                return $x === '' ? $unkAkre : $x;
            })
            ->map->count();

        $akreditasi = $akreRows->map(function (int $count, string $label) use ($akreColors, $unkAkre) {
            $key = $label === $unkAkre ? 'Belum' : $label;

            return [
                'label' => $label,
                'count' => $count,
                'color' => $akreColors[$key] ?? '#a855f7',
            ];
        })->sortByDesc(fn (array $a) => $a['count'])->values()->all();

        $desaGrouped = collect();
        $kelRows = Sekolah::query()
            ->whereIn('id', $sekolahIds)
            ->get(['nama_kecamatan', 'nama_kelurahan']);

        foreach ($kelRows as $row) {
            $k = trim((string) $row->nama_kecamatan) !== '' ? trim((string) $row->nama_kecamatan) : $unkKec;
            $kel = trim((string) $row->nama_kelurahan);
            if ($kel === '') {
                continue;
            }
            if (! $desaGrouped->has($k)) {
                $desaGrouped->put($k, collect());
            }
            /** @var Collection<int, string> $bucket */
            $bucket = $desaGrouped->get($k);
            if (! $bucket->contains($kel)) {
                $bucket->push($kel);
            }
        }
        $desaGrouped = $desaGrouped->map(fn (Collection $names) => $names->sort()->values());

        $lembagaPerKec = Sekolah::query()
            ->whereIn('id', $sekolahIds)
            ->selectRaw(
                'COALESCE(NULLIF(TRIM(nama_kecamatan), ""), ?) as kecamatan',
                [$unkKec]
            )
            ->selectRaw('COUNT(*) as c')
            ->groupBy('kecamatan')
            ->pluck('c', 'kecamatan');

        $rekapRows = $lembagaPerKec->keys()->map(function (string $kec) use ($lembagaPerKec, $desaGrouped) {
            $desa = $desaGrouped->get($kec, collect())->implode(', ');

            return [
                'kecamatan' => $kec,
                'jml_lembaga' => (int) $lembagaPerKec->get($kec, 0),
                'desa' => $desa !== '' ? $desa : '—',
            ];
        })->sortBy('kecamatan')->values()->all();

        return [
            'cabang' => $cabang,
            'total_lembaga' => $totalLembaga,
            'total_siswa' => $totalSiswa,
            'total_guru' => $totalGuru,
            'siswa_l' => $siswaL,
            'siswa_p' => $siswaP,
            'guru_l' => $guruL,
            'guru_p' => $guruP,
            'chart_kecamatan' => $chartKec,
            'akreditasi' => $akreditasi,
            'rekap_rows' => $rekapRows,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function emptyPayload(?Cabang $cabang): array
    {
        return [
            'cabang' => $cabang,
            'total_lembaga' => 0,
            'total_siswa' => 0,
            'total_guru' => 0,
            'siswa_l' => 0,
            'siswa_p' => 0,
            'guru_l' => 0,
            'guru_p' => 0,
            'chart_kecamatan' => [],
            'akreditasi' => [],
            'rekap_rows' => [],
        ];
    }
}
