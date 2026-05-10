<?php

namespace App\Http\Controllers\PengurusCabang;

use App\Http\Controllers\Controller;
use App\Models\LembagaRegistration;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LembagaRegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = LembagaRegistration::query()->with('cabang')->latest();

        if ($user->hasRole('pengurus_cabang') && $user->cabang_id) {
            $query->where('cabang_id', $user->cabang_id);
        }

        $registrations = $query->paginate(15)->withQueryString();

        return view('pengurus.lembaga-registrations.index', compact('registrations'));
    }

    public function show(Request $request, LembagaRegistration $lembagaRegistration): View
    {
        $this->authorizeCabang($request, $lembagaRegistration);

        $lembagaRegistration->load('permits', 'cabang');

        return view('pengurus.lembaga-registrations.show', [
            'reg' => $lembagaRegistration,
        ]);
    }

    public function approve(Request $request, LembagaRegistration $lembagaRegistration): RedirectResponse
    {
        $this->authorizeCabang($request, $lembagaRegistration);

        if ($lembagaRegistration->status !== LembagaRegistration::STATUS_PENDING_REVIEW) {
            return back()->withErrors(['approve' => __('Hanya permohonan yang menunggu verifikasi yang dapat disetujui.')]);
        }

        if (Sekolah::query()->where('npsn', $lembagaRegistration->npsn)->exists()) {
            return back()->withErrors(['approve' => __('NPSN sudah terdaftar sebagai sekolah aktif.')]);
        }

        if (User::query()->where('email', $lembagaRegistration->operator_email)->exists()) {
            return back()->withErrors(['approve' => __('Email operator sudah dipakai pengguna lain.')]);
        }

        $plainPassword = Str::password(20);

        $sekolah = DB::transaction(function () use ($lembagaRegistration, $plainPassword): Sekolah {
            $dusun = trim(collect([$lembagaRegistration->rt ? 'RT '.$lembagaRegistration->rt : null, $lembagaRegistration->rw ? 'RW '.$lembagaRegistration->rw : null])->filter()->implode(' '));

            $sekolah = Sekolah::query()->create([
                'cabang_id' => $lembagaRegistration->cabang_id,
                'npsn' => $lembagaRegistration->npsn,
                'nama' => $lembagaRegistration->nama_lembaga,
                'jenjang' => $lembagaRegistration->jenjang,
                'alamat' => $lembagaRegistration->alamat_jalan,
                'nama_provinsi' => $lembagaRegistration->provinsi,
                'nama_kabupaten' => $lembagaRegistration->kabupaten_kota,
                'nama_kecamatan' => $lembagaRegistration->kecamatan,
                'nama_kelurahan' => $lembagaRegistration->desa_kelurahan,
                'alamat_dusun' => $dusun !== '' ? $dusun : null,
                'telepon' => $lembagaRegistration->telepon,
                'email_kantor' => $lembagaRegistration->email,
                'website' => $lembagaRegistration->website,
                'npwp' => $lembagaRegistration->npwp,
                'medsos' => $lembagaRegistration->medsos,
                'tahun_berdiri' => $lembagaRegistration->tahun_berdiri,
                'waktu_belajar' => $lembagaRegistration->waktu_belajar,
                'status_kkm' => $lembagaRegistration->status_kkm,
                'komite' => $lembagaRegistration->komite,
                'rt' => $lembagaRegistration->rt,
                'rw' => $lembagaRegistration->rw,
                'kodepos' => $lembagaRegistration->kodepos,
                'kepala_nama' => $lembagaRegistration->nama_kepala,
                'is_active' => true,
            ]);

            $operator = User::query()->create([
                'name' => $lembagaRegistration->operator_name,
                'email' => $lembagaRegistration->operator_email,
                'password' => $plainPassword,
                'cabang_id' => $lembagaRegistration->cabang_id,
                'sekolah_id' => $sekolah->id,
                'email_verified_at' => now(),
            ]);

            $operator->assignRole('admin');

            $lembagaRegistration->forceFill([
                'status' => LembagaRegistration::STATUS_APPROVED,
                'sekolah_id' => $sekolah->id,
                'approved_at' => now(),
                'admin_notes' => null,
                'rejected_at' => null,
            ])->save();

            return $sekolah;
        });

        return redirect()
            ->route('pengurus.lembaga-registrations.show', $lembagaRegistration)
            ->with('status', __('Lembaga disetujui dan akun admin sekolah dibuat. Unduh e-sertifikat (PDF) pada detail permohonan bila perlu dicetak sebagai bukti verifikasi.'))
            ->with('operator_setup', [
                'email' => $lembagaRegistration->operator_email,
                'password' => $plainPassword,
                'sekolah' => $sekolah->nama,
            ]);
    }

    public function reject(Request $request, LembagaRegistration $lembagaRegistration): RedirectResponse
    {
        $this->authorizeCabang($request, $lembagaRegistration);

        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($lembagaRegistration->status !== LembagaRegistration::STATUS_PENDING_REVIEW) {
            return back()->withErrors(['reject' => __('Hanya permohonan yang menunggu verifikasi yang dapat ditolak.')]);
        }

        $lembagaRegistration->forceFill([
            'status' => LembagaRegistration::STATUS_REJECTED,
            'rejected_at' => now(),
            'admin_notes' => $request->input('admin_notes'),
        ])->save();

        return redirect()
            ->route('pengurus.lembaga-registrations.index')
            ->with('status', __('Permohonan ditandai ditolak.'));
    }

    private function authorizeCabang(Request $request, LembagaRegistration $lembagaRegistration): void
    {
        $user = $request->user();
        if ($user->hasRole('super_admin')) {
            return;
        }

        if ($user->hasRole('pengurus_cabang')) {
            abort_unless($user->cabang_id && (int) $lembagaRegistration->cabang_id === (int) $user->cabang_id, 403);

            return;
        }

        abort(403);
    }
}
