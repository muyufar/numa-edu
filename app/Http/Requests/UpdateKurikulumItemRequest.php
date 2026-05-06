<?php

namespace App\Http\Requests;

use App\Models\KurikulumItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKurikulumItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var KurikulumItem $kurikulumItem */
        $kurikulumItem = $this->route('kurikulum_item');

        return $this->user()?->can('update', $kurikulumItem) ?? false;
    }

    public function rules(): array
    {
        /** @var KurikulumItem $item */
        $item = $this->route('kurikulum_item');

        return [
            'mata_pelajaran_id' => [
                'required',
                'integer',
                'exists:mata_pelajarans,id',
                Rule::unique('kurikulum_items', 'mata_pelajaran_id')
                    ->where(function ($query) {
                        $query->where('tingkat', (int) $this->input('tingkat', 0))
                            ->where('semester', (string) $this->input('semester', ''))
                            ->where('tahun_ajaran', (string) $this->input('tahun_ajaran', ''));
                    })
                    ->ignore($item->id),
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
