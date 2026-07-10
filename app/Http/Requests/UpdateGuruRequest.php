<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesGtkProfile;
use App\Models\Guru;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateGuruRequest extends FormRequest
{
    use ValidatesGtkProfile;

    public function authorize(): bool
    {
        /** @var Guru $guru */
        $guru = $this->route('guru');

        return $this->user()?->can('update', $guru) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Guru $guru */
        $guru = $this->route('guru');

        return array_merge([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guru->user_id)],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'nip' => ['nullable', 'string', 'max:32', Rule::unique('gurus', 'nip')->ignore($guru->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'tugas' => ['nullable', 'string', 'max:128'],
            'mata_pelajaran' => ['nullable', 'string', 'max:255'],
            'penempatan' => ['nullable', 'string', 'max:255'],
            'total_jtm' => ['nullable', 'string', 'max:16'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hapus_foto' => ['sometimes', 'boolean'],
        ], $this->gtkProfileRules());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'hapus_foto' => $this->boolean('hapus_foto'),
        ]);
    }
}
