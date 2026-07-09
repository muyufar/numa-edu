<?php

namespace App\Http\Requests;

use App\Models\BkJenisPelanggaran;
use App\Support\BkTingkat;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBkJenisPelanggaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', BkJenisPelanggaran::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:32', 'alpha_dash'],
            'nama' => ['required', 'string', 'max:120'],
            'poin' => ['required', 'integer', 'min:0', 'max:999'],
            'tingkat' => ['required', 'string', Rule::in(BkTingkat::OPTIONS)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
