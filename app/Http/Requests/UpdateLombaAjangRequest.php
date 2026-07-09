<?php

namespace App\Http\Requests;

use App\Models\LombaAjang;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLombaAjangRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var LombaAjang $row */
        $row = $this->route('lomba_ajang');

        return $this->user()?->can('update', $row) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:160'],
            'tingkat' => ['nullable', 'string', 'max:64'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'lokasi' => ['nullable', 'string', 'max:160'],
            'penyelenggara' => ['nullable', 'string', 'max:160'],
            'keterangan' => ['nullable', 'string'],
            'siswa_ids' => ['nullable', 'array'],
            'siswa_ids.*' => ['integer', 'exists:siswas,id'],
        ];
    }
}
