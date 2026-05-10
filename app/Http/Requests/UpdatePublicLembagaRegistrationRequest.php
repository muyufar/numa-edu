<?php

namespace App\Http\Requests;

use App\Models\LembagaRegistration;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePublicLembagaRegistrationRequest extends FormRequest
{
    public ?LembagaRegistration $lembagaRegistration = null;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->lembagaRegistration = LembagaRegistration::query()
            ->where('public_token', (string) $this->route('token'))
            ->with('permits')
            ->firstOrFail();

        abort_unless(
            $this->lembagaRegistration->status === LembagaRegistration::STATUS_REJECTED,
            404
        );

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
        $reg = $this->lembagaRegistration;

        $fotoRules = function (string $pathColumn) use ($reg): array {
            $rules = ['nullable', 'file', 'image', 'max:5120'];
            if (! $reg->{$pathColumn}) {
                array_unshift($rules, 'required');
            }

            return $rules;
        };

        $rules = [
            'npsn' => [
                'required',
                'string',
                'regex:/^[0-9]{8}$/',
                Rule::unique('sekolahs', 'npsn'),
                Rule::unique('lembaga_registrations', 'npsn')
                    ->ignore($reg->id)
                    ->where(
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
            'foto_papan_nama' => $fotoRules('foto_papan_nama_path'),
            'foto_gedung_depan' => $fotoRules('foto_gedung_path'),
            'foto_kelas' => $fotoRules('foto_kelas_path'),
            'foto_halaman' => $fotoRules('foto_halaman_path'),
        ];

        foreach (LembagaRegistration::permitDefinitions() as $def) {
            $k = $def['key'];
            $rules["permits.$k.nomor_sk"] = ['nullable', 'string', 'max:255'];
            $rules["permits.$k.tanggal_sk"] = ['nullable', 'date'];
            $perm = $reg->permits->firstWhere('permit_key', $k);
            $pdf = ['nullable', 'file', 'mimes:pdf', 'max:12288'];
            if (! $perm?->dokumen_path) {
                array_unshift($pdf, 'required');
            }
            $rules["permits.$k.dokumen"] = $pdf;
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $reg = $this->lembagaRegistration;
            $email = (string) $v->getValue('operator_email');

            if ((string) $reg->operator_email !== $email && User::query()->where('email', $email)->exists()) {
                $v->errors()->add('operator_email', __('Email operator sudah terdaftar. Gunakan email lain.'));
            }
        });
    }
}
