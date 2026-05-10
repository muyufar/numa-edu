<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCabangMouSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'pengurus_cabang']) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cabang_id' => [
                Rule::requiredIf(fn () => $this->user()?->hasRole('super_admin') ?? false),
                'nullable',
                'integer',
                'exists:cabangs,id',
            ],
            'mou_lp_next_sequence' => ['required', 'integer', 'min:1'],
            'mou_lp_number_digits' => ['required', 'integer', 'min:1', 'max:8'],
            'mou_lp_number_suffix' => ['required', 'string', 'max:191'],
            'mou_penandatangan_nama' => ['nullable', 'string', 'max:191'],
            'mou_penandatangan_jabatan' => ['nullable', 'string', 'max:2000'],
            'mou_surat_kota' => ['nullable', 'string', 'max:100'],
            'mou_stempel' => ['nullable', 'file', 'image', 'max:4096'],
            'mou_penandatangan_ttd' => ['nullable', 'file', 'image', 'max:4096'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();
        if ($user && $user->hasRole('pengurus_cabang') && $user->cabang_id) {
            $this->merge(['cabang_id' => $user->cabang_id]);
        }
    }
}
