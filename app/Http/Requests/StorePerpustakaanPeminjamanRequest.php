<?php

namespace App\Http\Requests;

use App\Models\PerpustakaanBuku;
use App\Models\PerpustakaanPeminjaman;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePerpustakaanPeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PerpustakaanPeminjaman::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'perpustakaan_buku_id' => ['required', 'integer', 'exists:perpustakaan_bukus,id'],
            'tipe_peminjaman' => ['required', Rule::in(PerpustakaanPeminjaman::TIPE_OPTIONS)],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
