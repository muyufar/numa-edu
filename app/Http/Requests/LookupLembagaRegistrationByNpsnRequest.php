<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LookupLembagaRegistrationByNpsnRequest extends FormRequest
{
    protected $redirectRoute = 'public.lembaga-registrations.check-status';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $digits = preg_replace('/\D+/', '', (string) $this->input('npsn', ''));
        $this->merge(['npsn' => $digits]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'npsn' => ['required', 'string', 'regex:/^[0-9]{8}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'npsn.required' => __('Masukkan NPSN delapan digit.'),
            'npsn.regex' => __('NPSN harus tepat delapan digit angka.'),
        ];
    }
}
