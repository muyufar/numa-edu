<?php

namespace App\Support;

use App\Models\KewajibanPembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TagihanGenerator
{
    /**
     * Generate tagihan bulanan untuk 1 sekolah (opsional dibatasi kelas).
     *
     * @return array{created:int, skipped:int}
     */
    public static function generateBulananForSekolah(string $periode, int $sekolahId, ?int $kelasId = null): array
    {
        $created = 0;
        $skipped = 0;

        [$tahun, $bulan] = self::splitPeriode($periode);

        $kewajiban = KewajibanPembayaran::query()
            ->withoutGlobalScopes()
            ->where('sekolah_id', $sekolahId)
            ->where('is_active', true)
            ->where('tipe', 'bulanan')
            ->orderBy('nama')
            ->get();

        DB::transaction(function () use ($kewajiban, $periode, $tahun, $bulan, $sekolahId, $kelasId, &$created, &$skipped): void {
            $siswaQ = Siswa::query()
                ->withoutGlobalScopes()
                ->where('sekolah_id', $sekolahId)
                ->select(['id'])
                ->orderBy('id');

            if ($kelasId) {
                $siswaQ->where('kelas_id', $kelasId);
            }

            $siswaQ->chunkById(200, function ($siswas) use ($kewajiban, $periode, $tahun, $bulan, &$created, &$skipped): void {
                foreach ($siswas as $s) {
                    foreach ($kewajiban as $k) {
                        $exists = Tagihan::query()
                            ->withoutGlobalScopes()
                            ->where('siswa_id', $s->id)
                            ->where('periode', $periode)
                            ->where('jenis', $k->nama)
                            ->exists();

                        if ($exists) {
                            $skipped++;
                            continue;
                        }

                        $jatuhTempo = null;
                        if ($k->batas_hari_bayar) {
                            $jatuhTempo = Carbon::create($tahun, $bulan, (int) $k->batas_hari_bayar)->toDateString();
                        }

                        Tagihan::query()->create([
                            'siswa_id' => $s->id,
                            'jenis' => $k->nama,
                            'periode' => $periode,
                            'jumlah' => (float) $k->nominal_default,
                            'jatuh_tempo' => $jatuhTempo,
                            'status' => 'unpaid',
                        ]);
                        $created++;
                    }
                }
            });
        });

        return compact('created', 'skipped');
    }

    /**
     * @return array{0:int,1:int}
     */
    private static function splitPeriode(string $periode): array
    {
        if (! preg_match('/^(\d{4})\-(0[1-9]|1[0-2])$/', $periode, $m)) {
            abort(422, __('Format periode harus YYYY-MM.'));
        }

        return [(int) $m[1], (int) $m[2]];
    }
}

