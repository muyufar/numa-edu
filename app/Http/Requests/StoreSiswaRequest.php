<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Siswa::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'nis' => ['required', 'string', 'max:32', 'unique:siswas,nis'],
            'nisn' => ['nullable', 'string', 'max:32', 'unique:siswas,nisn'],
            'nik' => ['nullable', 'string', 'max:32'],
            'nama' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'tingkat_rombel' => ['nullable', 'string', 'max:255'],
            'umur' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'string', 'max:64'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:32'],
            'kebutuhan_khusus' => ['nullable', 'string', 'max:255'],
            'disabilitas' => ['nullable', 'string', 'max:255'],
            'nomor_kip_pip' => ['nullable', 'string', 'max:64'],
            'nama_ayah_kandung' => ['nullable', 'string', 'max:255'],
            'nama_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'nama_wali' => ['nullable', 'string', 'max:255'],
        ];
    }
}
