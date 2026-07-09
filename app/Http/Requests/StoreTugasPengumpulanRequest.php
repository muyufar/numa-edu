<?php

namespace App\Http\Requests;

use App\Models\Tugas;
use App\Models\TugasPilihan;
use App\Models\TugasSoal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTugasPengumpulanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tugas = $this->route('tugas');

        return $tugas instanceof Tugas
            && ($this->user()?->can('submit', $tugas) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Tugas $tugas */
        $tugas = $this->route('tugas');

        if ($tugas->isPilihanGanda()) {
            return [
                'jawaban' => ['required', 'array'],
                'jawaban.*' => ['required', 'integer', 'exists:tugas_pilihans,id'],
            ];
        }

        return [
            'jawaban_esai' => ['required', 'string', 'max:20000'],
            'file' => ['nullable', 'file', 'max:10240'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Tugas $tugas */
            $tugas = $this->route('tugas');

            if ($tugas->isPilihanGanda()) {
                $soalIds = $tugas->soals()->pluck('id')->all();
                $jawaban = (array) $this->input('jawaban', []);
                $answeredSoalIds = [];

                foreach ($jawaban as $soalId => $pilihanId) {
                    $soalId = (int) $soalId;
                    $pilihanId = (int) $pilihanId;

                    if (! in_array($soalId, $soalIds, true)) {
                        $validator->errors()->add('jawaban', __('Jawaban tidak valid.'));

                        return;
                    }

                    $pilihan = TugasPilihan::query()
                        ->whereKey($pilihanId)
                        ->where('tugas_soal_id', $soalId)
                        ->first();

                    if (! $pilihan) {
                        $validator->errors()->add("jawaban.{$soalId}", __('Pilihan jawaban tidak valid.'));

                        return;
                    }

                    $answeredSoalIds[] = $soalId;
                }

                if (count(array_unique($answeredSoalIds)) !== count($soalIds)) {
                    $validator->errors()->add('jawaban', __('Jawab semua soal sebelum mengumpulkan.'));
                }
            }
        });
    }
}
