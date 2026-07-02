<?php

namespace App\Support;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Support\Collection;

final class WaliKeuanganSummary
{
    /**
     * @return array{
     *     has_tunggakan: bool,
     *     total_sisa: float,
     *     jumlah_belum_lunas: int,
     *     harus_bayar: Collection<int, array{tagihan: Tagihan, sisa: float, is_overdue: bool}>,
     *     tagihans: Collection<int, Tagihan>,
     *     stats: array{total_tagihan: int, belum_lunas: int, lunas: int, total_dibayar: float}
     * }
     */
    public static function forSiswa(Siswa $siswa): array
    {
        $tagihans = $siswa->tagihans()
            ->withSum('pembayarans as total_dibayar', 'jumlah')
            ->orderByDesc('periode')
            ->orderByDesc('id')
            ->get();

        $harusBayar = $tagihans
            ->filter(fn (Tagihan $t) => in_array($t->status, ['unpaid', 'partial'], true))
            ->map(function (Tagihan $t) {
                $sisa = max(0, (float) $t->jumlah - (float) ($t->total_dibayar ?? 0));

                return [
                    'tagihan' => $t,
                    'sisa' => $sisa,
                    'is_overdue' => $t->jatuh_tempo !== null && $t->jatuh_tempo->isPast(),
                ];
            })
            ->sortBy([
                fn (array $row) => $row['is_overdue'] ? 0 : 1,
                fn (array $row) => $row['tagihan']->periode,
            ])
            ->values();

        $totalSisa = (float) $harusBayar->sum(fn (array $row) => $row['sisa']);

        $totalDibayar = (float) Pembayaran::query()
            ->whereHas('tagihan', fn ($q) => $q->where('siswa_id', $siswa->id))
            ->sum('jumlah');

        return [
            'has_tunggakan' => $harusBayar->isNotEmpty(),
            'total_sisa' => $totalSisa,
            'jumlah_belum_lunas' => $harusBayar->count(),
            'harus_bayar' => $harusBayar,
            'tagihans' => $tagihans,
            'stats' => [
                'total_tagihan' => $tagihans->count(),
                'belum_lunas' => $harusBayar->count(),
                'lunas' => $tagihans->where('status', 'paid')->count(),
                'total_dibayar' => $totalDibayar,
            ],
        ];
    }

    /**
     * @param  Collection<int, Siswa>  $siswas
     * @return array<int, array{count: int, total_sisa: float}>
     */
    public static function tunggakanBySiswaIds(Collection $siswas): array
    {
        if ($siswas->isEmpty()) {
            return [];
        }

        $ids = $siswas->pluck('id')->all();

        $rows = Tagihan::query()
            ->whereIn('siswa_id', $ids)
            ->whereIn('status', ['unpaid', 'partial'])
            ->withSum('pembayarans as total_dibayar', 'jumlah')
            ->get(['id', 'siswa_id', 'jumlah', 'status']);

        $out = [];
        foreach ($rows as $t) {
            $sid = (int) $t->siswa_id;
            if (! isset($out[$sid])) {
                $out[$sid] = ['count' => 0, 'total_sisa' => 0.0];
            }
            $out[$sid]['count']++;
            $out[$sid]['total_sisa'] += max(0, (float) $t->jumlah - (float) ($t->total_dibayar ?? 0));
        }

        return $out;
    }
}
