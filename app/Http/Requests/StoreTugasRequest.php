<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesTugasSoal;
use App\Models\Jadwal;
use App\Models\Tugas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTugasRequest extends FormRequest
{
    use ValidatesTugasSoal;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Tugas::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('kelas_id') === '') {
            $this->merge(['kelas_id' => null]);
        }
        if ($this->input('guru_id') === '') {
            $this->merge(['guru_id' => null]);
        }
        if ($this->input('hari') === '') {
            $this->merge(['hari' => null]);
        }
        if ($this->input('jam') === '') {
            $this->merge(['jam' => null]);
        }
        if ($this->input('tanggal_batas') === '') {
            $this->merge(['tanggal_batas' => null]);
        }
        if ($this->input('jam_batas') === '') {
            $this->merge(['jam_batas' => null]);
        }
        if ($this->input('bobot') === '') {
            $this->merge(['bobot' => null]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateTugasSoal($validator);
    }

    public function rules(): array
    {
        return array_merge([
            'mata_pelajaran_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'guru_id' => ['nullable', 'integer', 'exists:gurus,id'],
            'judul' => ['required', 'string', 'max:160'],
            'bahan_materi' => ['nullable', 'string', 'max:20000'],
            'instruksi' => ['nullable', 'string', 'max:4000'],
            'hari' => ['nullable', 'string', Rule::in(Jadwal::HARI_OPTIONS)],
            'jam' => ['nullable', 'date_format:H:i'],
            'tanggal_batas' => ['nullable', 'date'],
            'jam_batas' => ['nullable', 'date_format:H:i'],
            'semester' => ['nullable', Rule::in(['1', '2'])],
            'tahun_ajaran' => ['nullable', 'string', 'max:16'],
            'tipe' => ['required', Rule::in(Tugas::TIPE_OPTIONS)],
            'bobot' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'is_published' => ['sometimes', 'boolean'],
            'file' => ['nullable', 'file', 'max:10240'],
        ], $this->tugasSoalRules());
    }
}
