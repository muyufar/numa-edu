<?php

namespace App\Http\Requests;

use App\Models\InventarisBarang;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventarisBarangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'inventaris_kategori_id' => ['nullable', 'exists:inventaris_kategoris,id'],
            'nama' => ['required', 'string', 'max:160'],
            'kode' => ['nullable', 'string', 'max:64', 'unique:inventaris_barangs,kode'],
            'satuan' => ['required', 'string', 'max:32'],
            'stok_awal' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'kondisi' => ['required', 'string', Rule::in(InventarisBarang::KONDISI_OPTIONS)],
            'catatan' => ['nullable', 'string', 'max:5000'],
            'gambar' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
