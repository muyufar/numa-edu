<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesGtkProfile;
use App\Models\Pegawai;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePegawaiRequest extends FormRequest
{
    use ValidatesGtkProfile;

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
        return array_merge([
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:32'],
            'jabatan' => ['nullable', 'string', 'max:128'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hapus_foto' => ['sometimes', 'boolean'],
        ], $this->gtkProfileRules());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
            'hapus_foto' => $this->boolean('hapus_foto'),
        ]);
    }
}
