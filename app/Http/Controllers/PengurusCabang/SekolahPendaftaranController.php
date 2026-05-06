<?php

namespace App\Http\Controllers\PengurusCabang;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengurusCabang\StoreSekolahRequest;
use App\Models\Cabang;
use App\Models\Sekolah;
use App\Models\User;
use App\Support\SchoolOperator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SekolahPendaftaranController extends Controller
{
    public function create(): View
    {
        Gate::authorize('create', Sekolah::class);

        $user = auth()->user();
        $cabangs = $user->hasRole('super_admin')
            ? Cabang::query()->orderBy('nama')->get(['id', 'nama', 'kode'])
            : collect();

        $defaultOperatorDomain = config('tenancy.operator_email_domain', 'numa.com');

        return view('pengurus.sekolah-create', compact('cabangs', 'defaultOperatorDomain'));
    }

    public function store(StoreSekolahRequest $request): RedirectResponse
    {
        Gate::authorize('create', Sekolah::class);

        $user = $request->user();
        $validated = $request->validated();

        if ($user->hasRole('pengurus_cabang')) {
            abort_unless($user->cabang_id, 403);
            $cabangId = (int) $user->cabang_id;
        } else {
            $cabangId = (int) $validated['cabang_id'];
        }

        $email = ! empty($validated['operator_email'])
            ? (string) $validated['operator_email']
            : SchoolOperator::emailFromNpsn((string) $validated['npsn']);

        $plainPassword = Str::password(20);

        $sekolah = DB::transaction(function () use ($validated, $cabangId, $email, $plainPassword): Sekolah {
            $sekolah = Sekolah::query()->create([
                'cabang_id' => $cabangId,
                'npsn' => $validated['npsn'],
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
                'is_active' => true,
            ]);

            $operator = User::query()->create([
                'name' => $validated['operator_name'],
                'email' => $email,
                'password' => $plainPassword,
                'cabang_id' => $cabangId,
                'sekolah_id' => $sekolah->id,
                'email_verified_at' => now(),
            ]);

            $operator->assignRole('admin');

            return $sekolah;
        });

        return redirect()
            ->route('pengurus.sekolah.index')
            ->with('status', __('Sekolah :nama berhasil didaftarkan.', ['nama' => $sekolah->nama]))
            ->with('operator_setup', [
                'email' => $email,
                'password' => $plainPassword,
                'sekolah' => $sekolah->nama,
            ]);
    }
}
