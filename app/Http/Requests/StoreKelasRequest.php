<?php

namespace App\Http\Requests;

use App\Models\Guru;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreKelasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Kelas::class) ?? false;
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

            $sekolahId = $this->sekolahIdForValidation();
            $guruSekolahId = Guru::withoutGlobalScopes()->whereKey((int) $waliId)->value('sekolah_id');
            if ($guruSekolahId === null || (int) $guruSekolahId !== $sekolahId) {
                $validator->errors()->add('wali_kelas_id', __('Guru wali kelas harus berasal dari sekolah yang sama.'));
            }
        });
    }

    private function sekolahIdForValidation(): int
    {
        $user = $this->user();
        if ($user?->hasRole('pengurus_cabang') && session('pengurus_sekolah_id')) {
            return (int) session('pengurus_sekolah_id');
        }

        return (int) ($user?->sekolah_id ?? config('tenancy.default_sekolah_id', 1));
    }
}
