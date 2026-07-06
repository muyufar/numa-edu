<?php

namespace App\Http\Requests\Concerns;

use App\Models\Tugas;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

trait ValidatesTugasSoal
{
    /**
     * @return array<string, mixed>
     */
    protected function tugasSoalRules(): array
    {
        return [
            'jenis_soal' => ['required', Rule::in(Tugas::JENIS_SOAL_OPTIONS)],
            'soal' => ['nullable', 'array'],
            'soal.*.pertanyaan' => ['required_with:soal', 'string', 'max:2000'],
            'soal.*.jawaban_benar' => ['nullable', 'integer', 'min:0'],
            'soal.*.pilihan' => ['required_with:soal', 'array', 'min:2', 'max:6'],
            'soal.*.pilihan.*.teks' => ['required', 'string', 'max:500'],
        ];
    }

    protected function validateTugasSoal(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $jenis = (string) $this->input('jenis_soal', 'esai');

            if ($jenis === 'esai') {
                return;
            }

            /** @var array<int, array<string, mixed>>|null $soal */
            $soal = $this->input('soal');

            if (! is_array($soal) || count(array_filter($soal, fn ($item) => trim((string) ($item['pertanyaan'] ?? '')) !== '')) === 0) {
                $validator->errors()->add('soal', __('Minimal satu soal pilihan ganda wajib diisi.'));

                return;
            }

            foreach ($soal as $index => $item) {
                if (trim((string) ($item['pertanyaan'] ?? '')) === '') {
                    continue;
                }

                $pilihan = array_values($item['pilihan'] ?? []);
                $filled = array_filter($pilihan, fn ($p) => trim((string) ($p['teks'] ?? '')) !== '');

                if (count($filled) < 2) {
                    $validator->errors()->add("soal.{$index}.pilihan", __('Setiap soal minimal memiliki 2 pilihan jawaban.'));
                }

                $benarIndex = (int) ($item['jawaban_benar'] ?? -1);
                if ($benarIndex < 0 || $benarIndex >= count($pilihan) || trim((string) ($pilihan[$benarIndex]['teks'] ?? '')) === '') {
                    $validator->errors()->add("soal.{$index}.jawaban_benar", __('Tandai satu jawaban benar untuk setiap soal.'));
                }
            }
        });
    }
}
