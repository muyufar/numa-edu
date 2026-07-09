<?php

namespace App\Http\Requests;

use App\Models\BkPemanggilan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateBkPemanggilanRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var BkPemanggilan $row */
        $row = $this->route('bk_pemanggilan');

        return $this->user()?->can('update', $row) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $target = $this->input('target', 'siswa');
        $maxUrutan = BkPemanggilan::maxUrutan($target);

        return [
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'target' => ['required', 'string', Rule::in(BkPemanggilan::TARGET_OPTIONS)],
            'urutan' => ['required', 'integer', 'min:1', 'max:'.$maxUrutan],
            'tanggal_jadwal' => ['required', 'date'],
            'waktu' => ['nullable', 'date_format:H:i'],
            'tempat' => ['nullable', 'string', 'max:160'],
            'alasan' => ['required', 'string'],
            'status' => ['required', 'string', Rule::in(BkPemanggilan::STATUS_OPTIONS)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $target = (string) $this->input('target', 'siswa');
            $urutan = $this->integer('urutan');
            if ($urutan > BkPemanggilan::maxUrutan($target)) {
                $validator->errors()->add('urutan', __('Urutan melebihi batas untuk target ini.'));
            }
        });
    }
}
