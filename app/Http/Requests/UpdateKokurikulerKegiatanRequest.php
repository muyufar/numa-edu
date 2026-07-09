<?php

namespace App\Http\Requests;

use App\Models\KokurikulerKegiatan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKokurikulerKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var KokurikulerKegiatan $row */
        $row = $this->route('kokurikuler_kegiatan');

        return $this->user()?->can('update', $row) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'judul' => ['required', 'string', 'max:160'],
            'tempat' => ['nullable', 'string', 'max:160'],
            'tanggal' => ['required', 'date'],
            'laporan' => ['nullable', 'string'],
            'lkpd' => ['nullable', 'file', 'max:10240'],
            'status' => ['required', 'string', Rule::in(['draft', 'publish'])],
            'siswa_ids' => ['nullable', 'array'],
            'siswa_ids.*' => ['integer', 'exists:siswas,id'],
        ];
    }
}
