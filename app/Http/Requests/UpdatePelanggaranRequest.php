<?php

namespace App\Http\Requests;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePelanggaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Pelanggaran $pelanggaran */
        $pelanggaran = $this->route('pelanggaran');

        return $this->user()?->can('update', $pelanggaran) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
            /** @var Pelanggaran $pelanggaran */
            $pelanggaran = $this->route('pelanggaran');
            $kelasId = $pelanggaran->siswa?->kelas_id;
            $siswaId = $this->integer('siswa_id');
            if ($kelasId && $siswaId !== 0) {
                $ok = Siswa::query()->whereKey($siswaId)->where('kelas_id', $kelasId)->exists();
                if (! $ok) {
                    $validator->errors()->add('siswa_id', __('Siswa harus dari kelas yang sama dengan catatan ini.'));
                }
            }
        });
    }
}
