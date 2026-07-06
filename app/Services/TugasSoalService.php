<?php

namespace App\Services;

use App\Models\Tugas;
use Illuminate\Support\Facades\DB;

class TugasSoalService
{
    /**
     * @param  array<int, array{pertanyaan: string, jawaban_benar?: int|string, pilihan: array<int, array{teks: string}>}>|null  $soalPayload
     */
    public function sync(Tugas $tugas, string $jenisSoal, ?array $soalPayload): void
    {
        DB::transaction(function () use ($tugas, $jenisSoal, $soalPayload): void {
            $tugas->soals()->delete();

            if ($jenisSoal !== 'pilihan_ganda' || empty($soalPayload)) {
                return;
            }

            foreach (array_values($soalPayload) as $urutan => $item) {
                $pertanyaan = trim((string) ($item['pertanyaan'] ?? ''));
                if ($pertanyaan === '') {
                    continue;
                }

                $soal = $tugas->soals()->create([
                    'urutan' => $urutan + 1,
                    'pertanyaan' => $pertanyaan,
                ]);

                $pilihanItems = array_values($item['pilihan'] ?? []);
                $benarIndex = (int) ($item['jawaban_benar'] ?? 0);

                foreach ($pilihanItems as $pi => $pilihan) {
                    $teks = trim((string) ($pilihan['teks'] ?? ''));
                    if ($teks === '') {
                        continue;
                    }

                    $soal->pilihans()->create([
                        'label' => chr(65 + $pi),
                        'teks' => $teks,
                        'is_benar' => $pi === $benarIndex,
                    ]);
                }
            }
        });
    }
}
