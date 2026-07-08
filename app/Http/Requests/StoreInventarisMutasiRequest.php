<?php

namespace App\Http\Requests;

use App\Models\InventarisMutasi;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventarisMutasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'admin', 'guru']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('tipe') !== 'in') {
            $this->merge(['sumber_pengadaan' => null]);
        }
        if ($this->input('sumber_pengadaan') === '') {
            $this->merge(['sumber_pengadaan' => null]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'inventaris_barang_id' => ['required', 'exists:inventaris_barangs,id'],
            'tanggal' => ['required', 'date'],
            'tipe' => ['required', 'string', Rule::in(InventarisMutasi::TIPE_OPTIONS)],
            'sumber_pengadaan' => [
                'nullable',
                'required_if:tipe,in',
                'string',
                Rule::in(InventarisMutasi::SUMBER_PENGADAAN_OPTIONS),
            ],
            'jumlah' => ['required', 'integer', 'min:1'],
            'referensi' => ['nullable', 'string', 'max:120'],
            'keterangan' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
