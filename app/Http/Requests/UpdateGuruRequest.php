<?php

namespace App\Http\Requests;

use App\Models\Guru;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateGuruRequest extends FormRequest
{
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

        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guru->user_id)],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'nip' => ['nullable', 'string', 'max:32', Rule::unique('gurus', 'nip')->ignore($guru->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
