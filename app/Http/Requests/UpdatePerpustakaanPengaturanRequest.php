<?php

namespace App\Http\Requests;

use App\Models\PerpustakaanPengaturan;
use App\Support\PolicyRoles;
use Illuminate\Foundation\Http\FormRequest;

use App\Support\PolicyRoles;

class UpdatePerpustakaanPengaturanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PolicyRoles::perpusTim($this->user());
    }

    public function rules(): array
    {
        return [
            'max_peminjaman_aktif' => ['required', 'integer', 'min:1', 'max:20'],
            'masa_pinjam_fisik_hari' => ['required', 'integer', 'min:1', 'max:90'],
            'masa_pinjam_digital_hari' => ['required', 'integer', 'min:1', 'max:180'],
            'denda_per_hari' => ['required', 'integer', 'min:0', 'max:1000000'],
            'max_perpanjangan' => ['required', 'integer', 'min:0', 'max:5'],
        ];
    }
}
