<?php

namespace App\Http\Requests;

use App\Models\Guru;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('kelas')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('wali_kelas_id') === '' || $this->input('wali_kelas_id') === null) {
            $this->merge(['wali_kelas_id' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tingkat' => ['required', 'integer', 'min:1', 'max:12'],
            'nama' => ['required', 'string', 'max:64'],
            'tahun_ajaran' => ['required', 'string', 'max:16'],
            'wali_kelas_id' => ['nullable', 'integer', 'exists:gurus,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $waliId = $this->input('wali_kelas_id');
            if ($waliId === null || $waliId === '') {
                return;
            }

            $kelas = $this->route('kelas');
            $guruSekolahId = Guru::withoutGlobalScopes()->whereKey((int) $waliId)->value('sekolah_id');
            if ($guruSekolahId === null || (int) $guruSekolahId !== (int) $kelas->sekolah_id) {
                $validator->errors()->add('wali_kelas_id', __('Guru wali kelas harus berasal dari sekolah yang sama.'));
            }
        });
    }
}
