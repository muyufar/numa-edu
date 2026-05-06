<?php

namespace App\Http\Requests;

use App\Models\PpdbRegistration;
use App\Models\Siswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PromoteSiswaFromPpdbRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var PpdbRegistration $registration */
        $registration = $this->route('ppdb_registration');

        if (! $this->user()?->can('update', $registration)) {
            return false;
        }

        if (! $this->user()?->can('create', Siswa::class)) {
            return false;
        }

        return $registration->status === 'accepted'
            && ! $registration->siswa()->exists();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:32', 'unique:siswas,nis'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
        ];
    }
}
