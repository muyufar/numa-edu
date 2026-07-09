<?php

namespace App\Http\Requests;

use App\Models\PerpustakaanKategori;
use Illuminate\Foundation\Http\FormRequest;

class StorePerpustakaanKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PerpustakaanKategori::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:120'],
            'kode' => ['nullable', 'string', 'max:32'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
