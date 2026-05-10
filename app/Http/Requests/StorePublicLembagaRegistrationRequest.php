<?php

namespace App\Http\Requests;

use App\Models\LembagaRegistration;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePublicLembagaRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach ([
            'npwp', 'telepon', 'website', 'email', 'medsos', 'nama_kepala',
            'alamat_jalan', 'rt', 'rw', 'desa_kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi', 'kodepos',
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
        $rules = [
            'npsn' => [
                'required',
                'string',
                'regex:/^[0-9]{8}$/',
                Rule::unique('sekolahs', 'npsn'),
                Rule::unique('lembaga_registrations', 'npsn')->where(
                    fn ($q) => $q->whereIn('status', [
                        LembagaRegistration::STATUS_AWAITING_MOU,
                        LembagaRegistration::STATUS_PENDING_REVIEW,
                    ])
                ),
            ],
            'nama_lembaga' => ['required', 'string', 'max:255'],
            'nama_kepala' => ['nullable', 'string', 'max:255'],
            'jenjang' => ['required', 'string', Rule::in(Sekolah::JENJANG_KEYS)],
            'npwp' => ['nullable', 'string', 'max:32'],
            'telepon' => ['nullable', 'string', 'max:64'],
            'website' => ['nullable', 'string', 'max:512'],
            'email' => ['nullable', 'email', 'max:255'],
            'medsos' => ['nullable', 'string', 'max:512'],
            'tahun_berdiri' => ['nullable', 'integer', 'min:1900', 'max:'.(int) date('Y')],
            'waktu_belajar' => ['required', 'string', Rule::in(['pagi', 'siang', 'pagi_siang'])],
            'status_kkm' => ['required', 'string', Rule::in(['induk', 'anggota', 'tidak'])],
            'komite' => ['required', 'string', Rule::in(['sudah', 'belum'])],
            'jumlah_murid' => ['required', 'integer', 'min:0', 'max:999999'],
            'alamat_jalan' => ['required', 'string', 'max:2000'],
            'rt' => ['nullable', 'string', 'max:8'],
            'rw' => ['nullable', 'string', 'max:8'],
            'desa_kelurahan' => ['required', 'string', 'max:191'],
            'kecamatan' => ['required', 'string', 'max:191'],
            'kabupaten_kota' => ['required', 'string', 'max:191'],
            'provinsi' => ['required', 'string', 'max:191'],
            'kodepos' => ['nullable', 'string', 'max:16'],
            'operator_name' => ['required', 'string', 'max:255'],
            'operator_email' => ['required', 'email', 'max:255'],
            'foto_papan_nama' => ['required', 'file', 'image', 'max:5120'],
            'foto_gedung_depan' => ['required', 'file', 'image', 'max:5120'],
            'foto_kelas' => ['required', 'file', 'image', 'max:5120'],
            'foto_halaman' => ['required', 'file', 'image', 'max:5120'],
        ];

        foreach (LembagaRegistration::permitDefinitions() as $def) {
            $k = $def['key'];
            $rules["permits.$k.nomor_sk"] = ['nullable', 'string', 'max:255'];
            $rules["permits.$k.tanggal_sk"] = ['nullable', 'date'];
            $rules["permits.$k.dokumen"] = ['nullable', 'file', 'mimes:pdf', 'max:12288'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $email = (string) $v->getValue('operator_email');
            if (User::query()->where('email', $email)->exists()) {
                $v->errors()->add('operator_email', __('Email operator sudah terdaftar. Gunakan email lain.'));
            }
        });
    }
}
