<?php

namespace App\Http\Requests;

use App\Models\MateriAjar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMateriAjarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', MateriAjar::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'mata_pelajaran_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'guru_id' => ['nullable', 'integer', 'exists:gurus,id'],

            'judul' => ['required', 'string', 'max:160'],
            'deskripsi' => ['nullable', 'string', 'max:4000'],
            'semester' => ['nullable', Rule::in(['1', '2'])],
            'tahun_ajaran' => ['nullable', 'string', 'max:16'],
            'tanggal' => ['nullable', 'date'],

            'file' => ['required', 'file', 'max:10240'], // 10 MB
        ];
    }
}

