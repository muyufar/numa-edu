<?php

namespace App\Http\Requests;

use App\Models\PresensiPegawai;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePresensiPegawaiBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PresensiPegawai::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'presensi' => ['required', 'array', 'min:1'],
            'presensi.*.pegawai_id' => ['required', 'integer', 'exists:pegawais,id'],
            'presensi.*.status' => ['required', 'string', 'max:16', Rule::in(PresensiPegawai::STATUS_OPTIONS)],
            'presensi.*.keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
