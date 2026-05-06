<?php

namespace App\Http\Requests;

use App\Models\PresensiGuru;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePresensiGuruBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PresensiGuru::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'presensi' => ['required', 'array', 'min:1'],
            'presensi.*.guru_id' => ['required', 'integer', 'exists:gurus,id'],
            'presensi.*.status' => ['required', 'string', 'max:16', Rule::in(PresensiGuru::STATUS_OPTIONS)],
            'presensi.*.keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
