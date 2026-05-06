<?php

namespace App\Http\Requests;

use App\Models\Pegawai;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Pegawai $pegawai */
        $pegawai = $this->route('pegawai');

        return $this->user()?->can('update', $pegawai) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:32'],
            'jabatan' => ['nullable', 'string', 'max:128'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }
}
