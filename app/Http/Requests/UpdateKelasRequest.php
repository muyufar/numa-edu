<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('kelas')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tingkat' => ['required', 'integer', 'min:1', 'max:12'],
            'nama' => ['required', 'string', 'max:64'],
            'tahun_ajaran' => ['required', 'string', 'max:16'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
