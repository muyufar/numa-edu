<?php

namespace App\Http\Requests;

use App\Models\PresensiSiswa;
use App\Models\Siswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePresensiSiswaBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PresensiSiswa::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'tanggal' => ['required', 'date'],
            'presensi' => ['required', 'array', 'min:1'],
            'presensi.*.siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'presensi.*.status' => ['required', 'string', 'max:16', Rule::in(PresensiSiswa::STATUS_OPTIONS)],
            'presensi.*.keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $kelasId = (int) $this->input('kelas_id');
            if ($kelasId === 0) {
                return;
            }
            foreach ($this->input('presensi', []) as $i => $row) {
                if (! isset($row['siswa_id'])) {
                    continue;
                }
                $ok = Siswa::query()
                    ->whereKey((int) $row['siswa_id'])
                    ->where('kelas_id', $kelasId)
                    ->exists();
                if (! $ok) {
                    $v->errors()->add(
                        "presensi.$i.siswa_id",
                        __('Siswa tidak termasuk kelas yang dipilih.')
                    );
                }
            }
        });
    }
}
