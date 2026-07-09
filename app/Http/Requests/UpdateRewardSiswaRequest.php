<?php

namespace App\Http\Requests;

use App\Models\RewardSiswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRewardSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var RewardSiswa $row */
        $row = $this->route('reward_siswa');

        return $this->user()?->can('update', $row) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'siswa_id' => ['required', 'integer', 'exists:siswas,id'],
            'kategori' => ['required', 'string', Rule::in(RewardSiswa::KATEGORI_OPTIONS)],
            'judul' => ['required', 'string', 'max:160'],
            'poin' => ['required', 'integer', 'min:0', 'max:999'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ];
    }
}
