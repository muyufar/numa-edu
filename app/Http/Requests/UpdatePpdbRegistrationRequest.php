<?php

namespace App\Http\Requests;

use App\Models\PpdbRegistration;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePpdbRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var PpdbRegistration $ppdbRegistration */
        $ppdbRegistration = $this->route('ppdb_registration');

        return $this->user()?->can('update', $ppdbRegistration) ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['tempat_lahir', 'alamat', 'asal_sekolah', 'no_hp_ortu', 'email'] as $f) {
            if ($this->input($f) === '') {
                $this->merge([$f => null]);
            }
        }
        if ($this->input('tanggal_lahir') === '') {
            $this->merge(['tanggal_lahir' => null]);
        }
        if ($this->input('jenis_kelamin') === '') {
            $this->merge(['jenis_kelamin' => null]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:128'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'alamat' => ['nullable', 'string'],
            'asal_sekolah' => ['nullable', 'string', 'max:255'],
            'no_hp_ortu' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'string', 'email', 'max:128'],
            'status' => ['required', 'string', 'max:32', Rule::in(PpdbRegistration::STATUS_OPTIONS)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var PpdbRegistration $registration */
            $registration = $this->route('ppdb_registration');
            if ($registration->siswa()->exists() && $this->input('status') !== 'accepted') {
                $validator->errors()->add(
                    'status',
                    __('Status tidak dapat diubah dari Diterima karena sudah menjadi data siswa.')
                );
            }
        });
    }
}
