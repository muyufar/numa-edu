<?php

namespace App\Http\Requests;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PromoteSiswaKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Siswa::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'kelas_asal_id' => ['required', 'integer', 'exists:kelas,id'],
            'kelas_tujuan_id' => ['required', 'integer', 'exists:kelas,id', 'different:kelas_asal_id'],
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['integer', 'distinct', 'exists:siswas,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $asalId = (int) $this->input('kelas_asal_id');
            $tujuanId = (int) $this->input('kelas_tujuan_id');

            $asal = Kelas::query()->find($asalId);
            $tujuan = Kelas::query()->find($tujuanId);

            if ($asal && $tujuan && (int) $asal->sekolah_id !== (int) $tujuan->sekolah_id) {
                $validator->errors()->add('kelas_tujuan_id', __('Kelas tujuan harus berada di sekolah yang sama.'));
            }

            foreach ((array) $this->input('siswa_ids', []) as $index => $id) {
                $siswa = Siswa::query()->find((int) $id);

                if (! $siswa) {
                    continue;
                }

                if ((int) $siswa->kelas_id !== $asalId) {
                    $validator->errors()->add(
                        "siswa_ids.$index",
                        __('Siswa :nama tidak berada di kelas asal.', ['nama' => $siswa->nama])
                    );
                }

                if ($siswa->isAlumni()) {
                    $validator->errors()->add(
                        "siswa_ids.$index",
                        __('Siswa :nama sudah berstatus alumni/lulus.', ['nama' => $siswa->nama])
                    );
                }
            }
        });
    }
}
