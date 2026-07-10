<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesGtkProfile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreGuruRequest extends FormRequest
{
    use ValidatesGtkProfile;

    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Guru::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'nip' => ['nullable', 'string', 'max:32', 'unique:gurus,nip'],
            'phone' => ['nullable', 'string', 'max:20'],
            'tugas' => ['nullable', 'string', 'max:128'],
            'mata_pelajaran' => ['nullable', 'string', 'max:255'],
            'penempatan' => ['nullable', 'string', 'max:255'],
            'total_jtm' => ['nullable', 'string', 'max:16'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], $this->gtkProfileRules());
    }
}
