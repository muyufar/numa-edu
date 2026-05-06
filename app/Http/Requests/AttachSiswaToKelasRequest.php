<?php

namespace App\Http\Requests;

use App\Models\Kelas;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachSiswaToKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        $kelas = $this->route('kelas');

        return $kelas instanceof Kelas && $this->user()?->can('update', $kelas);
    }

    protected function prepareForValidation(): void
    {
        $ids = $this->input('siswa_ids');
        if ($ids !== null && ! is_array($ids)) {
            $this->merge(['siswa_ids' => [$ids]]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Kelas $kelas */
        $kelas = $this->route('kelas');

        return [
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => [
                'integer',
                Rule::exists('siswas', 'id')
                    ->where('sekolah_id', (int) $kelas->sekolah_id)
                    ->whereNull('kelas_id'),
            ],
        ];
    }
}
