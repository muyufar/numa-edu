<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\Models\Pegawai;
use App\Support\GtkDetail;
use App\Support\GtkFoto;
use App\Support\GtkProfilePayload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function show(Pegawai $pegawai): View
    {
        Gate::authorize('view', $pegawai);

        return view('tenaga-kependidikan.show', [
            'gtk' => GtkDetail::fromPegawai($pegawai),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Pegawai::class);

        return view('pegawai.create');
    }

    public function store(StorePegawaiRequest $request): RedirectResponse
    {
        Pegawai::query()->create($request->validated());

        return redirect()
            ->route('tenaga-kependidikan.index', ['tab' => 'pegawai'])
            ->with('status', __('Pegawai ditambahkan.'));
    }

    public function edit(Pegawai $pegawai): View
    {
        Gate::authorize('update', $pegawai);

        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai): RedirectResponse
    {
        $data = $request->validated();

        $gtkAttributes = GtkProfilePayload::gtkAttributes($data);
        $gtkAttributes['nama'] = $data['nama'];
        $gtkAttributes['nip'] = $data['nip'] ?? null;
        $gtkAttributes['jabatan'] = $data['jabatan'] ?? null;
        $gtkAttributes['phone'] = $data['phone'] ?? null;
        $gtkAttributes['is_active'] = $data['is_active'] ?? false;

        if ($request->boolean('hapus_foto')) {
            GtkFoto::delete($pegawai->foto_path);
            $gtkAttributes['foto_path'] = null;
            $gtkAttributes['foto_name'] = null;
        }

        if ($request->hasFile('foto')) {
            $gtkAttributes = array_merge($gtkAttributes, GtkFoto::store($pegawai, $request->file('foto')));
        }

        $pegawai->update($gtkAttributes);

        return redirect()
            ->route('pegawai.show', $pegawai)
            ->with('status', __('Data tenaga kependidikan berhasil diperbarui.'));
    }

    public function destroy(Pegawai $pegawai): RedirectResponse
    {
        Gate::authorize('delete', $pegawai);

        GtkFoto::delete($pegawai->foto_path);
        $pegawai->delete();

        return redirect()
            ->route('tenaga-kependidikan.index', ['tab' => 'pegawai'])
            ->with('status', __('Pegawai dihapus.'));
    }
}
