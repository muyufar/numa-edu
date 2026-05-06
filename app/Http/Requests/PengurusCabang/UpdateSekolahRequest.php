<?php

namespace App\Http\Requests\PengurusCabang;

use App\Models\Sekolah;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateSekolahRequest extends FormRequest
{
    public function authorize(): bool
    {
        $sekolah = $this->route('sekolah');

        return $sekolah instanceof Sekolah && $this->user()?->can('update', $sekolah);
    }

    protected function prepareForValidation(): void
    {
        foreach ([
            'alamat', 'telepon', 'email_kantor', 'website',
            'kepala_nama', 'kepala_nip', 'akreditasi', 'akreditasi_tahun',
            'kode_provinsi', 'nama_provinsi', 'kode_kabupaten', 'nama_kabupaten',
            'kode_kecamatan', 'nama_kecamatan', 'kode_kelurahan', 'nama_kelurahan',
            'alamat_dusun',
        ] as $f) {
            if ($this->input($f) === '') {
                $this->merge([$f => null]);
            }
        }

        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'string', Rule::in(Sekolah::JENJANG_KEYS)],
            'kode_provinsi' => ['nullable', 'string', 'max:16', 'regex:/^[0-9]+$/'],
            'nama_provinsi' => ['nullable', 'string', 'max:191'],
            'kode_kabupaten' => ['nullable', 'string', 'max:24', 'regex:/^[0-9]+(\.[0-9]+)*$/'],
            'nama_kabupaten' => ['nullable', 'string', 'max:191'],
            'kode_kecamatan' => ['nullable', 'string', 'max:24', 'regex:/^[0-9]+(\.[0-9]+)*$/'],
            'nama_kecamatan' => ['nullable', 'string', 'max:191'],
            'kode_kelurahan' => ['nullable', 'string', 'max:24', 'regex:/^[0-9]+(\.[0-9]+)*$/'],
            'nama_kelurahan' => ['nullable', 'string', 'max:191'],
            'alamat_dusun' => ['nullable', 'string', 'max:2000'],
            'alamat' => ['nullable', 'string', 'max:2000'],
            'telepon' => ['nullable', 'string', 'max:32'],
            'email_kantor' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:512'],
            'kepala_nama' => ['nullable', 'string', 'max:255'],
            'kepala_nip' => ['nullable', 'string', 'max:32'],
            'akreditasi' => ['nullable', 'string', 'max:8'],
            'akreditasi_tahun' => ['nullable', 'string', 'max:8'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $sekolah = $this->route('sekolah');
            if (! $sekolah instanceof Sekolah) {
                return;
            }

            $defaultId = (int) config('tenancy.default_sekolah_id', 1);
            if ((int) $sekolah->id === $defaultId && ! $v->getValue('is_active')) {
                $v->errors()->add(
                    'is_active',
                    __('Sekolah bawaan sistem tidak boleh dinonaktifkan.')
                );
            }
        });
    }
}
