<?php

namespace App\Http\Requests;

use App\Models\RewardSiswa;
use App\Models\Siswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRewardSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', RewardSiswa::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'kategori' => ['required', 'string', Rule::in(RewardSiswa::KATEGORI_OPTIONS)],
            'judul' => ['required', 'string', 'max:160'],
            'poin' => ['required', 'integer', 'min:0', 'max:999'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
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
