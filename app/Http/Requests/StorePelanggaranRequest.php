<?php

namespace App\Http\Requests;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePelanggaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Pelanggaran::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'string', 'max:64', Rule::in(Pelanggaran::JENIS_KEYS)],
            'deskripsi' => ['nullable', 'string'],
            'tindakan' => ['nullable', 'string'],
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
        });
    }
}
