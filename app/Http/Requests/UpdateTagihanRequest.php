<?php

namespace App\Http\Requests;

use App\Models\Tagihan;
use App\Support\PeriodeBulan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTagihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Tagihan $tagihan */
        $tagihan = $this->route('tagihan');

        return $this->user()?->can('update', $tagihan) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('jatuh_tempo') === '') {
            $this->merge(['jatuh_tempo' => null]);
        }

        $periode = $this->input('periode');
        if (is_string($periode)) {
            $normalized = PeriodeBulan::normalize($periode);
            if ($normalized) {
                $this->merge(['periode' => $normalized]);
            }
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'jenis' => ['required', 'string', 'max:32'],
            'periode' => ['required', 'string', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'jatuh_tempo' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'periode.regex' => __('Format periode harus YYYY-MM (contoh: 2026-04).'),
        ];
    }
}
