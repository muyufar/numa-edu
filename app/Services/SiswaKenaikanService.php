<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SiswaKenaikanService
{
    /**
     * @param  list<int>  $siswaIds
     */
    public function promote(Kelas $asal, Kelas $tujuan, array $siswaIds): int
    {
        $tingkatRombel = trim($tujuan->tingkat.' '.$tujuan->nama);
        $count = 0;

        DB::transaction(function () use ($asal, $tujuan, $siswaIds, $tingkatRombel, &$count): void {
            foreach ($siswaIds as $id) {
                $siswa = Siswa::query()->whereKey($id)->lockForUpdate()->firstOrFail();

                if ((int) $siswa->kelas_id !== (int) $asal->id) {
                    continue;
                }

                if ($siswa->isAlumni()) {
                    continue;
                }

                Gate::authorize('update', $siswa);

                $siswa->update([
                    'kelas_id' => $tujuan->id,
                    'tingkat_rombel' => $tingkatRombel !== '' ? $tingkatRombel : $siswa->tingkat_rombel,
                    'status' => $siswa->status && trim($siswa->status) !== '' ? $siswa->status : 'Aktif',
                ]);

                $count++;
            }
        });

        return $count;
    }

    /**
     * @param  list<int>  $siswaIds
     */
    public function graduate(Kelas $kelas, array $siswaIds, string $status): int
    {
        $count = 0;

        DB::transaction(function () use ($kelas, $siswaIds, $status, &$count): void {
            foreach ($siswaIds as $id) {
                $siswa = Siswa::query()->whereKey($id)->lockForUpdate()->firstOrFail();

                if ((int) $siswa->kelas_id !== (int) $kelas->id) {
                    continue;
                }

                if ($siswa->isAlumni()) {
                    continue;
                }

                Gate::authorize('update', $siswa);

                $siswa->update([
                    'status' => $status,
                ]);

                $count++;
            }
        });

        return $count;
    }
}
