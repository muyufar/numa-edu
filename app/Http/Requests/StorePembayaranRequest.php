<?php

namespace App\Http\Requests;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Tagihan $tagihan */
        $tagihan = $this->route('tagihan');

        return $this->user()?->can('update', $tagihan) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('referensi') === '') {
            $this->merge(['referensi' => null]);
        }
        if ($this->input('dibayar_pada') === '') {
            $this->merge(['dibayar_pada' => null]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'metode' => ['required', 'string', 'max:32', Rule::in(Pembayaran::METODE_OPTIONS)],
            'referensi' => ['nullable', 'string', 'max:255'],
            'dibayar_pada' => ['nullable', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var Tagihan $tagihan */
            $tagihan = $this->route('tagihan');
            $tagihan->refresh();
            $sisa = $tagihan->sisa();
            $bayar = (float) $this->input('jumlah');
            if ($bayar > $sisa + 0.00001) {
                $v->errors()->add('jumlah', __('Jumlah melebihi sisa tagihan (:sisa).', ['sisa' => number_format($sisa, 2, ',', '.')]));
            }
        });
    }
}
