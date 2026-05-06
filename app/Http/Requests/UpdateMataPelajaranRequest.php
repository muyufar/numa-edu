<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMataPelajaranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('mataPelajaran')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('mataPelajaran')?->id;

        return [
            'kode' => ['required', 'string', 'max:16', 'alpha_dash', 'unique:mata_pelajarans,kode,' . $id],
            'nama' => ['required', 'string', 'max:255'],
        ];
    }
}
