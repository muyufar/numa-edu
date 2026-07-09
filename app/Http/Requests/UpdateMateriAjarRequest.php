<?php

namespace App\Http\Requests;

use App\Models\MateriAjar;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMateriAjarRequest extends FormRequest
{
    use Concerns\ValidatesMateriAjarRequest;

    public function authorize(): bool
    {
        /** @var MateriAjar $materiAjar */
        $materiAjar = $this->route('materi_ajar');

        return $this->user()?->can('update', $materiAjar) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareMateriAjarFields();
    }

    public function rules(): array
    {
        return $this->materiAjarRules(fileRequired: false);
    }

    public function withValidator($validator): void
    {
        $this->validateModulAjarContent($validator);
    }
}
