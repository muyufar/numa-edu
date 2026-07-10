<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesGtkProfile;
use App\Models\Pegawai;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    use ValidatesGtkProfile;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Pegawai::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:32'],
            'jabatan' => ['nullable', 'string', 'max:128'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], $this->gtkProfileRules());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }
}
