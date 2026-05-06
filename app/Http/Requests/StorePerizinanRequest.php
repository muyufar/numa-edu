<?php

namespace App\Http\Requests;

use App\Models\Perizinan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePerizinanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Perizinan::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $statusRule = $user && $user->hasAnyRole(['super_admin', 'admin'])
            ? Rule::in(Perizinan::STATUS_OPTIONS)
            : Rule::in(['pending']);

        return [
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'string', 'max:32', Rule::in(Perizinan::JENIS_OPTIONS)],
            'keterangan' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:16', $statusRule],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $kelasId = $this->integer('kelas_id');
            $siswaId = $this->integer('siswa_id');
            if ($kelasId !== 0 && $siswaId !== 0) {
                $ok = \App\Models\Siswa::query()->whereKey($siswaId)->where('kelas_id', $kelasId)->exists();
                if (! $ok) {
                    $validator->errors()->add('siswa_id', __('Siswa tidak termasuk kelas yang dipilih.'));
                }
            }
        });
    }
}
