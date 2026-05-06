<?php

namespace App\Http\Requests\PengurusCabang;

use App\Models\Sekolah;
use App\Models\User;
use App\Support\SchoolOperator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSekolahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Sekolah::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach ([
            'alamat', 'telepon', 'email_kantor', 'website',
            'kepala_nama', 'kepala_nip', 'akreditasi', 'akreditasi_tahun',
            'operator_email',
            'kode_provinsi', 'nama_provinsi', 'kode_kabupaten', 'nama_kabupaten',
            'kode_kecamatan', 'nama_kecamatan', 'kode_kelurahan', 'nama_kelurahan',
            'alamat_dusun',
        ] as $f) {
            if ($this->input($f) === '') {
                $this->merge([$f => null]);
            }
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $cabangIdRules = $user && $user->hasRole('super_admin')
            ? ['required', 'integer', 'exists:cabangs,id']
            : ['prohibited'];

        return [
            'cabang_id' => $cabangIdRules,
            'npsn' => ['required', 'string', 'max:16', 'regex:/^[0-9]{8}$/', 'unique:sekolahs,npsn'],
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
            'operator_name' => ['required', 'string', 'max:255'],
            'operator_email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $npsn = (string) $v->getValue('npsn');
            $override = $v->getValue('operator_email');
            $email = $override ? (string) $override : SchoolOperator::emailFromNpsn($npsn);

            if (User::query()->where('email', $email)->exists()) {
                $v->errors()->add(
                    $override ? 'operator_email' : 'npsn',
                    __('Alamat email operator sudah dipakai. Gunakan email lain atau NPSN lain.')
                );
            }
        });
    }
}
