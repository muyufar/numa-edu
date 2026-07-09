<?php

namespace App\Http\Requests;

use App\Models\PerpustakaanKategori;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePerpustakaanKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var PerpustakaanKategori $kategori */
        $kategori = $this->route('perpustakaan_kategori');

        return $this->user()?->can('update', $kategori) ?? false;
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
