<?php

namespace App\Support;

use App\Models\AkuntansiAkun;
use App\Models\AkuntansiJurnal;
use App\Models\AkuntansiJurnalLine;
use Illuminate\Support\Facades\DB;

class ManualJurnalService
{
    /**
     * @param  array<int, array{akun_id: int, debit: float, kredit: float}>  $lines
     */
    public static function create(int $sekolahId, int $userId, string $tanggal, ?string $noBukti, ?string $keterangan, array $lines): AkuntansiJurnal
    {
        return DB::transaction(function () use ($sekolahId, $userId, $tanggal, $noBukti, $keterangan, $lines): AkuntansiJurnal {
            $jurnal = AkuntansiJurnal::query()->create([
                'sekolah_id' => $sekolahId,
                'tanggal' => $tanggal,
                'no_bukti' => $noBukti,
                'keterangan' => $keterangan,
                'sumber_type' => null,
                'sumber_id' => null,
                'dibuat_oleh' => $userId,
            ]);

            foreach ($lines as $row) {
                AkuntansiJurnalLine::query()->create([
                    'sekolah_id' => $sekolahId,
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $row['akun_id'],
                    'debit' => $row['debit'],
                    'kredit' => $row['kredit'],
                ]);
            }

            return $jurnal->fresh('lines.akun');
        });
    }

    /**
     * @param  array<int, mixed>  $rawLines
     * @return array{0: bool, 1: string|null, 2: list<array{akun_id: int, debit: float, kredit: float}>}
     */
    public static function normalizeLines(int $sekolahId, array $rawLines): array
    {
        $normalized = [];

        foreach ($rawLines as $row) {
            if (! is_array($row)) {
                continue;
            }
            $akunId = isset($row['akun_id']) ? (int) $row['akun_id'] : 0;
            if ($akunId <= 0) {
                continue;
            }

            $debit = round((float) ($row['debit'] ?? 0), 2);
            $kredit = round((float) ($row['kredit'] ?? 0), 2);

            if ($debit <= 0 && $kredit <= 0) {
                continue;
            }

            if ($debit > 0 && $kredit > 0) {
                return [false, __('Setiap baris hanya boleh berisi debit atau kredit, bukan keduanya.'), []];
            }

            $exists = AkuntansiAkun::query()
                ->where('sekolah_id', $sekolahId)
                ->where('is_active', true)
                ->whereKey($akunId)
                ->exists();

            if (! $exists) {
                return [false, __('Akun tidak valid atau tidak aktif.'), []];
            }

            $normalized[] = [
                'akun_id' => $akunId,
                'debit' => $debit,
                'kredit' => $kredit,
            ];
        }

        if (count($normalized) < 2) {
            return [false, __('Isi minimal dua baris jurnal dengan akun dan nominal.'), []];
        }

        $sumDebit = round(array_sum(array_column($normalized, 'debit')), 2);
        $sumKredit = round(array_sum(array_column($normalized, 'kredit')), 2);

        if (abs($sumDebit - $sumKredit) > 0.01) {
            return [false, __('Total debit (:d) harus sama dengan total kredit (:k).', [
                'd' => number_format($sumDebit, 2, ',', '.'),
                'k' => number_format($sumKredit, 2, ',', '.'),
            ]), []];
        }

        if ($sumDebit <= 0) {
            return [false, __('Total debit/kredit harus lebih dari nol.'), []];
        }

        return [true, null, $normalized];
    }

    public static function isManual(AkuntansiJurnal $jurnal): bool
    {
        return $jurnal->sumber_type === null && $jurnal->sumber_id === null;
    }

    public static function jurnalsForExport(int $sekolahId, string $tanggalFrom, string $tanggalTo)
    {
        return AkuntansiJurnal::query()
            ->where('sekolah_id', $sekolahId)
            ->whereBetween('tanggal', [$tanggalFrom, $tanggalTo])
            ->with(['lines' => fn ($q) => $q->with('akun:id,kode,nama')])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();
    }

    public static function sumberLabel(?string $sumberType): string
    {
        if ($sumberType === null) {
            return __('Jurnal manual');
        }

        return match ($sumberType) {
            \App\Models\Pembayaran::class => __('Pembayaran'),
            \App\Models\PengeluaranKas::class => __('Pengeluaran kas'),
            default => class_basename($sumberType),
        };
    }
}
