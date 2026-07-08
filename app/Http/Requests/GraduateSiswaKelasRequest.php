<?php

namespace App\Http\Requests;

use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class GraduateSiswaKelasRequest extends FormRequest
{
    public const STATUS_OPTIONS = ['Lulus', 'Alumni', 'Tamat'];

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
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['integer', 'distinct', 'exists:siswas,id'],
            'status' => ['required', 'string', Rule::in(self::STATUS_OPTIONS)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $kelasId = (int) $this->input('kelas_id');

            foreach ((array) $this->input('siswa_ids', []) as $index => $id) {
                $siswa = Siswa::query()->find((int) $id);

                if (! $siswa) {
                    continue;
                }

                if ((int) $siswa->kelas_id !== $kelasId) {
                    $validator->errors()->add(
                        "siswa_ids.$index",
                        __('Siswa :nama tidak berada di kelas yang dipilih.', ['nama' => $siswa->nama])
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
