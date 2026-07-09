<?php

namespace App\Http\Requests;

use App\Models\BkHomeVisit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBkHomeVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var BkHomeVisit $row */
        $row = $this->route('bk_home_visit');

        return $this->user()?->can('update', $row) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'tanggal' => ['required', 'date'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'catatan_wawancara' => ['nullable', 'string'],
            'hasil_kunjungan' => ['nullable', 'string'],
            'solusi' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:16'],
        ];
    }
}
