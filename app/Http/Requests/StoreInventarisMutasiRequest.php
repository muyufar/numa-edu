<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventarisMutasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'admin', 'guru']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'inventaris_barang_id' => ['required', 'exists:inventaris_barangs,id'],
            'tanggal' => ['required', 'date'],
            'tipe' => ['required', 'string', Rule::in(\App\Models\InventarisMutasi::TIPE_OPTIONS)],
            'jumlah' => ['required', 'integer', 'min:1'],
            'referensi' => ['nullable', 'string', 'max:120'],
            'keterangan' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
