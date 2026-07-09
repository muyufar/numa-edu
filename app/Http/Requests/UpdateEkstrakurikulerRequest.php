<?php

namespace App\Http\Requests;

use App\Models\Ekstrakurikuler;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEkstrakurikulerRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Ekstrakurikuler $row */
        $row = $this->route('ekstrakurikuler');

        return $this->user()?->can('update', $row) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:160'],
            'guru_id' => ['nullable', 'integer', 'exists:gurus,id'],
            'hari' => ['nullable', 'string', 'max:32'],
            'jam' => ['nullable', 'string', 'max:32'],
            'lokasi' => ['nullable', 'string', 'max:120'],
            'is_active' => ['sometimes', 'boolean'],
            'siswa_ids' => ['nullable', 'array'],
            'siswa_ids.*' => ['integer', 'exists:siswas,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
