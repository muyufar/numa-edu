<?php

namespace App\Http\Requests;

use App\Models\LembagaRegistration;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePengurusLembagaRegistrationPermitsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [];

        foreach (LembagaRegistration::permitDefinitions() as $def) {
            $k = $def['key'];
            $rules["permits.$k.nomor_sk"] = ['nullable', 'string', 'max:255'];
            $rules["permits.$k.tanggal_sk"] = ['nullable', 'date'];
            $rules["permits.$k.dokumen"] = ['nullable', 'file', 'mimes:pdf', 'max:12288'];
        }

        return $rules;
    }
}
