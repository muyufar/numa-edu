<?php

namespace App\Http\Requests;

use App\Models\BkPemanggilan;
use App\Models\Siswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBkPemanggilanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', BkPemanggilan::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $target = $this->input('target', 'siswa');
        $maxUrutan = BkPemanggilan::maxUrutan($target);

        return [
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
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
            $kelasId = $this->integer('kelas_id');
            $siswaId = $this->integer('siswa_id');
            if ($kelasId !== 0 && $siswaId !== 0) {
                $ok = Siswa::query()->whereKey($siswaId)->where('kelas_id', $kelasId)->exists();
                if (! $ok) {
                    $validator->errors()->add('siswa_id', __('Siswa tidak termasuk kelas yang dipilih.'));
                }
            }

            $target = (string) $this->input('target', 'siswa');
            $urutan = $this->integer('urutan');
            if ($urutan > BkPemanggilan::maxUrutan($target)) {
                $validator->errors()->add('urutan', __('Urutan melebihi batas untuk target ini.'));
            }
        });
    }
}
