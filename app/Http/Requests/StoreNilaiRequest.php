<?php

namespace App\Http\Requests;

use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreNilaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Nilai::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $v = $this->input('nilai_akhir');
        if ($v === '') {
            $this->merge(['nilai_akhir' => null]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'mata_pelajaran_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'semester' => ['required', 'string', 'max:8', Rule::in(Nilai::SEMESTER_OPTIONS)],
            'tahun_ajaran' => ['required', 'string', 'max:16'],
            'nilai_akhir' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $siswaId = (int) $this->input('siswa_id');
            $kelasId = (int) $this->input('kelas_id');
            if ($siswaId === 0 || $kelasId === 0) {
                return;
            }
            $ok = Siswa::query()->whereKey($siswaId)->where('kelas_id', $kelasId)->exists();
            if (! $ok) {
                $v->errors()->add('siswa_id', __('Siswa tidak berada di kelas yang dipilih.'));
            }

            if (Nilai::query()
                ->where('siswa_id', $this->input('siswa_id'))
                ->where('mata_pelajaran_id', $this->input('mata_pelajaran_id'))
                ->where('semester', $this->input('semester'))
                ->where('tahun_ajaran', $this->input('tahun_ajaran'))
                ->exists()) {
                $v->errors()->add('siswa_id', __('Nilai untuk kombinasi siswa, mapel, semester, dan tahun ajaran ini sudah ada.'));
            }
        });
    }
}
