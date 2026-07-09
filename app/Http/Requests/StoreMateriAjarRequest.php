<?php

namespace App\Http\Requests;

use App\Models\MateriAjar;
use Illuminate\Foundation\Http\FormRequest;

class StoreMateriAjarRequest extends FormRequest
{
    use Concerns\ValidatesMateriAjarRequest;

    public function authorize(): bool
    {
        return $this->user()?->can('create', MateriAjar::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareMateriAjarFields();
    }

    public function rules(): array
    {
        return $this->materiAjarRules(fileRequired: ! \App\Support\PerangkatAjarJenis::supportsKontenDigital($this->input('jenis')));
    }

    public function withValidator($validator): void
    {
        $this->validateModulAjarContent($validator);
    }
}
