<?php

namespace App\Http\Requests;

use App\Models\KurikulumItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKurikulumItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', KurikulumItem::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'mata_pelajaran_id' => [
                'required',
                'integer',
                'exists:mata_pelajarans,id',
                Rule::unique('kurikulum_items', 'mata_pelajaran_id')->where(function ($query) {
                    $query->where('tingkat', (int) $this->input('tingkat', 0))
                        ->where('semester', (string) $this->input('semester', ''))
                        ->where('tahun_ajaran', (string) $this->input('tahun_ajaran', ''));
                }),
            ],
            'tingkat' => ['required', 'integer', 'min:1', 'max:12'],
            'semester' => ['required', Rule::in(KurikulumItem::SEMESTER_OPTIONS)],
            'tahun_ajaran' => ['required', 'string', 'max:16'],
            'jam_per_minggu' => ['nullable', 'integer', 'min:0', 'max:40'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
