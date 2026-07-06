<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AkunOnboardingController extends Controller
{
    public function show(): View|RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if (! $user->needsHubungkanAkunSekolahOnboarding()) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.hubungkan', [
            'user' => $user,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if (! $user->needsHubungkanAkunSekolahOnboarding()) {
            return redirect()->route('dashboard');
        }

        $rules = [
            'npsn' => ['required', 'string', 'max:16'],
            'nis' => ['required', 'string', 'max:32'],
            'tanggal_lahir' => ['required', 'date'],
            'nama_siswa' => ['required', 'string', 'max:255'],
        ];
        if ($user->hasRole('wali')) {
            $rules['hubungan'] = ['required', 'string', 'max:32'];
        }
        $data = $request->validate($rules);

        $npsnTrim = trim($data['npsn']);
        $npsnDigits = preg_replace('/\D/', '', $npsnTrim) ?? '';

        $sekolah = Sekolah::query()
            ->where('is_active', true)
            ->where(function ($q) use ($npsnTrim, $npsnDigits) {
                $q->where('npsn', $npsnTrim);
                if ($npsnDigits !== '') {
                    $q->orWhere('npsn', $npsnDigits);
                }
            })
            ->first();

        if (! $sekolah) {
            return back()
                ->withInput()
                ->withErrors(['npsn' => __('Sekolah tidak ditemukan atau tidak aktif. Periksa NPSN.')]);
        }

        $namaNorm = static::normalizeNama($data['nama_siswa']);

        $identifier = trim($data['nis']);

        $siswa = Siswa::withoutGlobalScopes()
            ->where('sekolah_id', $sekolah->id)
            ->whereDate('tanggal_lahir', $data['tanggal_lahir'])
            ->where(function ($q) use ($identifier) {
                $q->where('nis', $identifier)
                    ->orWhere('nisn', $identifier);
            })
            ->first();

        if (! $siswa || static::normalizeNama($siswa->nama) !== $namaNorm) {
            return back()
                ->withInput()
                ->withErrors(['nis' => __('Data tidak cocok. Pastikan NIS/NISN, tanggal lahir, dan nama sesuai data sekolah.')]);
        }

        if ($user->hasRole('siswa') && ! $user->siswa()->exists()) {
            if ($siswa->user_id !== null && (int) $siswa->user_id !== (int) $user->id) {
                return back()
                    ->withInput()
                    ->withErrors(['nis' => __('Akun siswa untuk data ini sudah dihubungkan pengguna lain. Hubungi admin sekolah.')]);
            }

            if ($siswa->user_id === null) {
                $siswa->forceFill(['user_id' => $user->id])->save();
            }

            $user->forceFill([
                'sekolah_id' => $siswa->sekolah_id,
                'cabang_id' => $sekolah->cabang_id,
            ])->save();
            $user->refresh();
        }

        if ($user->hasRole('wali') && ! $user->waliSiswas()->exists()) {
            if ($user->sekolah_id && (int) $user->sekolah_id !== (int) $siswa->sekolah_id) {
                return back()
                    ->withInput()
                    ->withErrors(['npsn' => __('Akun ini sudah tertaut ke sekolah lain. Hubungi admin jika perlu menambah anak di sekolah berbeda.')]);
            }

            $user->waliSiswas()->attach($siswa->id, ['hubungan' => $data['hubungan']]);

            $user->forceFill([
                'sekolah_id' => $siswa->sekolah_id,
                'cabang_id' => $sekolah->cabang_id,
            ])->save();
            $user->refresh();
        }

        $user->refresh();

        if (! $user->needsHubungkanAkunSekolahOnboarding()) {
            return redirect()->route('dashboard')->with('status', __('Akun berhasil dihubungkan ke data sekolah.'));
        }

        return redirect()
            ->route('onboarding.hubungkan')
            ->with('status', __('Satu langkah lagi: lengkapi penautan akun Anda.'));
    }

    private static function normalizeNama(string $nama): string
    {
        $s = preg_replace('/\s+/u', ' ', trim($nama)) ?? '';

        return Str::lower($s);
    }
}
