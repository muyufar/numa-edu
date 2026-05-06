<?php

namespace App\Http\Requests;

use App\Models\KinerjaPenilaian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKinerjaPenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var KinerjaPenilaian $kinerjaPenilaian */
        $kinerjaPenilaian = $this->route('kinerja_penilaian');

        return $this->user()?->can('update', $kinerjaPenilaian) ?? false;
    }

    public function rules(): array
    {
        return [
            'target_type' => ['required', Rule::in(KinerjaPenilaian::TARGET_TYPES)],
            'guru_id' => ['nullable', 'integer', 'exists:gurus,id', 'required_if:target_type,guru'],
            'pegawai_id' => ['nullable', 'integer', 'exists:pegawais,id', 'required_if:target_type,pegawai'],

            'tanggal' => ['required', 'date'],
            'periode' => ['required', 'string', 'regex:/^\d{4}\-\d{2}$/'],

            'aspek' => ['required', 'string', 'max:120'],
            'skor' => ['required', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('target_type') === 'guru') {
            $this->merge(['pegawai_id' => null]);
        }

        if ($this->input('target_type') === 'pegawai') {
            $this->merge(['guru_id' => null]);
        }
    }
}

