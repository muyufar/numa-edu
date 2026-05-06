<?php

namespace App\Http\Controllers\PengurusCabang;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengurusCabang\UpdateSekolahRequest;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SekolahProfilController extends Controller
{
    public function edit(Sekolah $sekolah): View
    {
        Gate::authorize('update', $sekolah);

        $sekolah->loadMissing('cabang:id,nama');

        return view('pengurus.sekolah-edit', [
            'sekolah' => $sekolah,
            'isAdminProfilLembaga' => false,
        ]);
    }

    public function update(UpdateSekolahRequest $request, Sekolah $sekolah): RedirectResponse
    {
        $validated = $request->validated();

        $sekolah->update([
            'nama' => $validated['nama'],
            'jenjang' => $validated['jenjang'],
            'kode_provinsi' => $validated['kode_provinsi'] ?? null,
            'nama_provinsi' => $validated['nama_provinsi'] ?? null,
            'kode_kabupaten' => $validated['kode_kabupaten'] ?? null,
            'nama_kabupaten' => $validated['nama_kabupaten'] ?? null,
            'kode_kecamatan' => $validated['kode_kecamatan'] ?? null,
            'nama_kecamatan' => $validated['nama_kecamatan'] ?? null,
            'kode_kelurahan' => $validated['kode_kelurahan'] ?? null,
            'nama_kelurahan' => $validated['nama_kelurahan'] ?? null,
            'alamat_dusun' => $validated['alamat_dusun'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'email_kantor' => $validated['email_kantor'] ?? null,
            'website' => $validated['website'] ?? null,
            'kepala_nama' => $validated['kepala_nama'] ?? null,
            'kepala_nip' => $validated['kepala_nip'] ?? null,
            'akreditasi' => $validated['akreditasi'] ?? null,
            'akreditasi_tahun' => $validated['akreditasi_tahun'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('pengurus.sekolah.index')
            ->with('status', __('Profil sekolah diperbarui.'));
    }
}
