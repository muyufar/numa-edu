<?php

namespace App\Http\Requests;

use App\Models\BkSanksi;
use App\Support\BkTingkat;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBkSanksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var BkSanksi $row */
        $row = $this->route('bk_sanksi');

        return $this->user()?->can('update', $row) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:120'],
            'tingkat' => ['required', 'string', Rule::in(BkTingkat::OPTIONS)],
            'deskripsi' => ['nullable', 'string'],
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
