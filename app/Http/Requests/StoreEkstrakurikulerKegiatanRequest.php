<?php

namespace App\Http\Requests;

use App\Models\Ekstrakurikuler;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEkstrakurikulerKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Ekstrakurikuler $ekskul */
        $ekskul = $this->route('ekstrakurikuler');

        return $this->user()?->can('update', $ekskul) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'judul' => ['required', 'string', 'max:160'],
            'laporan' => ['nullable', 'string'],
        ];
    }
}
