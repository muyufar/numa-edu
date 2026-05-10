<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SignPublicLembagaMouRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mou_nomor_sekolah' => ['required', 'string', 'max:191'],
            'mou_accepted' => ['required', 'accepted'],
        ];
    }
}
