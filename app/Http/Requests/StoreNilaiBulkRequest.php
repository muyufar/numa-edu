<?php

namespace App\Http\Requests;

use App\Models\Nilai;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNilaiBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Nilai::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $rows = $this->input('nilai', []);
        if (! is_array($rows)) {
            return;
        }
        foreach ($rows as $i => $row) {
            if (! is_array($row)) {
                continue;
            }
            if (($row['nilai_akhir'] ?? null) === '') {
                $rows[$i]['nilai_akhir'] = null;
            }
        }
        $this->merge(['nilai' => $rows]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $kelasId = $this->input('kelas_id');

        return [
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'semester' => ['required', 'string', 'max:8', Rule::in(Nilai::SEMESTER_OPTIONS)],
            'tahun_ajaran' => ['required', 'string', 'max:16'],
            'nilai' => ['required', 'array', 'min:1'],
            'nilai.*.siswa_id' => [
                'required',
                'integer',
                Rule::exists('siswas', 'id')->where('kelas_id', $kelasId),
            ],
            'nilai.*.nilai_akhir' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
