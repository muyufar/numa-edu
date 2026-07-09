<?php

namespace App\Http\Requests;

use App\Models\PerpustakaanBuku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerpustakaanBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var PerpustakaanBuku $buku */
        $buku = $this->route('perpustakaan_buku');

        return $this->user()?->can('update', $buku) ?? false;
    }

    public function rules(): array
    {
        /** @var PerpustakaanBuku $buku */
        $buku = $this->route('perpustakaan_buku');
        $needsFile = in_array($this->input('tipe', $buku->tipe), ['digital', 'fisik_digital'], true)
            && ! $buku->file_path
            && ! $this->hasFile('file');

        return [
            'perpustakaan_kategori_id' => ['nullable', 'integer', 'exists:perpustakaan_kategoris,id'],
            'judul' => ['required', 'string', 'max:200'],
            'pengarang' => ['nullable', 'string', 'max:160'],
            'penerbit' => ['nullable', 'string', 'max:120'],
            'tahun_terbit' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'isbn' => ['nullable', 'string', 'max:32'],
            'tipe' => ['required', Rule::in(PerpustakaanBuku::TIPE_OPTIONS)],
            'jumlah_eksemplar' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'rak_lokasi' => ['nullable', 'string', 'max:64'],
            'bahasa' => ['nullable', 'string', 'max:16'],
            'sinopsis' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
            'cover' => ['nullable', 'image', 'max:5120'],
            'file' => [
                $needsFile ? 'required' : 'nullable',
                'file',
                'mimes:pdf',
                'max:30720',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('perpustakaan_kategori_id') === '') {
            $this->merge(['perpustakaan_kategori_id' => null]);
        }

        if ($this->input('tahun_terbit') === '') {
            $this->merge(['tahun_terbit' => null]);
        }
    }
}
