<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicLembagaRegistrationRequest;
use App\Http\Requests\UpdatePublicLembagaRegistrationRequest;
use App\Models\LembagaRegistration;
use App\Models\LembagaRegistrationPermit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PendaftaranLembagaPublicController extends Controller
{
    public function create(): View
    {
        $permitDefs = LembagaRegistration::permitDefinitions();

        return view('public.lembaga-register', [
            'permitDefs' => $permitDefs,
            'wizardInitialStep' => $this->wizardInitialStepFromErrors($permitDefs),
            'wizardFileSkips' => [],
        ]);
    }

    public function edit(string $token): View
    {
        $registration = LembagaRegistration::query()
            ->where('public_token', $token)
            ->with('permits')
            ->firstOrFail();

        abort_unless($registration->status === LembagaRegistration::STATUS_REJECTED, 404);

        $permitDefs = LembagaRegistration::permitDefinitions();

        $wizardFileSkips = [
            'foto_papan_nama' => (bool) $registration->foto_papan_nama_path,
            'foto_gedung_depan' => (bool) $registration->foto_gedung_path,
            'foto_kelas' => (bool) $registration->foto_kelas_path,
            'foto_halaman' => (bool) $registration->foto_halaman_path,
        ];

        return view('public.lembaga-register', [
            'permitDefs' => $permitDefs,
            'registration' => $registration,
            'isEdit' => true,
            'wizardInitialStep' => $this->wizardInitialStepFromErrors($permitDefs),
            'wizardFileSkips' => $wizardFileSkips,
        ]);
    }

    /**
     * @param  list<array{key: string, label: string}>  $permitDefs
     */
    private function wizardInitialStepFromErrors(array $permitDefs): int
    {
        $errors = session('errors');
        if ($errors === null || ! $errors->any()) {
            return 1;
        }

        if ($errors->has('operator_name') || $errors->has('operator_email')) {
            return 5;
        }

        foreach ($errors->keys() as $key) {
            if (is_string($key) && str_starts_with($key, 'permits.')) {
                return 4;
            }
        }

        foreach (['foto_papan_nama', 'foto_gedung_depan', 'foto_kelas', 'foto_halaman'] as $f) {
            if ($errors->has($f)) {
                return 3;
            }
        }

        foreach (['alamat_jalan', 'rt', 'rw', 'desa_kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi', 'kodepos'] as $f) {
            if ($errors->has($f)) {
                return 2;
            }
        }

        foreach (['npsn', 'nama_lembaga', 'nama_kepala', 'jenjang', 'npwp', 'telepon', 'website', 'email', 'medsos', 'tahun_berdiri', 'waktu_belajar', 'status_kkm', 'komite', 'jumlah_murid'] as $f) {
            if ($errors->has($f)) {
                return 1;
            }
        }

        return 1;
    }

    public function store(StorePublicLembagaRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $permitsInput = $request->input('permits', []);

        $cabangId = (int) (config('lembaga.default_cabang_id', 1));

        $registration = DB::transaction(function () use ($request, $validated, $permitsInput, $cabangId): LembagaRegistration {
            $token = (string) Str::uuid();
            $baseDir = 'lembaga-registrations/'.$token;

            $paths = [
                'foto_papan_nama_path' => $request->file('foto_papan_nama')->store($baseDir.'/galeri', 'public'),
                'foto_gedung_path' => $request->file('foto_gedung_depan')->store($baseDir.'/galeri', 'public'),
                'foto_kelas_path' => $request->file('foto_kelas')->store($baseDir.'/galeri', 'public'),
                'foto_halaman_path' => $request->file('foto_halaman')->store($baseDir.'/galeri', 'public'),
            ];

            /** @var LembagaRegistration $reg */
            $reg = LembagaRegistration::query()->create(array_merge([
                'public_token' => $token,
                'status' => LembagaRegistration::STATUS_AWAITING_MOU,
                'cabang_id' => $cabangId,
                'npsn' => $validated['npsn'],
                'nama_lembaga' => $validated['nama_lembaga'],
                'nama_kepala' => $validated['nama_kepala'] ?? null,
                'jenjang' => $validated['jenjang'],
                'npwp' => $validated['npwp'] ?? null,
                'telepon' => $validated['telepon'] ?? null,
                'website' => $validated['website'] ?? null,
                'email' => $validated['email'] ?? null,
                'medsos' => $validated['medsos'] ?? null,
                'tahun_berdiri' => $validated['tahun_berdiri'] ?? null,
                'waktu_belajar' => $validated['waktu_belajar'],
                'status_kkm' => $validated['status_kkm'],
                'komite' => $validated['komite'],
                'jumlah_murid' => $validated['jumlah_murid'],
                'alamat_jalan' => $validated['alamat_jalan'] ?? null,
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'desa_kelurahan' => $validated['desa_kelurahan'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kabupaten_kota' => $validated['kabupaten_kota'] ?? null,
                'provinsi' => $validated['provinsi'] ?? null,
                'kodepos' => $validated['kodepos'] ?? null,
                'operator_name' => $validated['operator_name'],
                'operator_email' => $validated['operator_email'],
            ], $paths));

            $order = 0;
            foreach (LembagaRegistration::permitDefinitions() as $def) {
                $key = $def['key'];
                $row = is_array($permitsInput[$key] ?? null) ? $permitsInput[$key] : [];
                $nomor = isset($row['nomor_sk']) && $row['nomor_sk'] !== '' ? (string) $row['nomor_sk'] : null;
                $tanggal = isset($row['tanggal_sk']) && $row['tanggal_sk'] !== '' ? $row['tanggal_sk'] : null;
                $dokPath = null;
                if ($request->hasFile("permits.$key.dokumen")) {
                    $dokPath = $request->file("permits.$key.dokumen")->store($baseDir.'/perizinan', 'public');
                }

                LembagaRegistrationPermit::query()->create([
                    'lembaga_registration_id' => $reg->id,
                    'sort_order' => $order++,
                    'permit_key' => $key,
                    'nama_sk' => $def['label'],
                    'nomor_sk' => $nomor,
                    'tanggal_sk' => $tanggal,
                    'dokumen_path' => $dokPath,
                ]);
            }

            return $reg;
        });

        return redirect()
            ->route('public.lembaga-registrations.mou', ['token' => $registration->public_token]);
    }

    public function update(UpdatePublicLembagaRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $permitsInput = $request->input('permits', []);

        /** @var LembagaRegistration $reg */
        $reg = $request->lembagaRegistration;

        DB::transaction(function () use ($request, $validated, $permitsInput, $reg): void {
            $disk = Storage::disk('public');
            $baseDir = 'lembaga-registrations/'.$reg->public_token;

            $photoMap = [
                'foto_papan_nama' => 'foto_papan_nama_path',
                'foto_gedung_depan' => 'foto_gedung_path',
                'foto_kelas' => 'foto_kelas_path',
                'foto_halaman' => 'foto_halaman_path',
            ];

            $pathUpdates = [];
            foreach ($photoMap as $input => $column) {
                if (! $request->hasFile($input)) {
                    continue;
                }

                $prev = $reg->{$column};
                if (is_string($prev) && $prev !== '') {
                    $disk->delete($prev);
                }

                $pathUpdates[$column] = $request->file($input)->store($baseDir.'/galeri', 'public');
            }

            $nextStatus = $reg->mou_signed_at
                ? LembagaRegistration::STATUS_PENDING_REVIEW
                : LembagaRegistration::STATUS_AWAITING_MOU;

            $reg->forceFill(array_merge([
                'npsn' => $validated['npsn'],
                'nama_lembaga' => $validated['nama_lembaga'],
                'nama_kepala' => $validated['nama_kepala'] ?? null,
                'jenjang' => $validated['jenjang'],
                'npwp' => $validated['npwp'] ?? null,
                'telepon' => $validated['telepon'] ?? null,
                'website' => $validated['website'] ?? null,
                'email' => $validated['email'] ?? null,
                'medsos' => $validated['medsos'] ?? null,
                'tahun_berdiri' => $validated['tahun_berdiri'] ?? null,
                'waktu_belajar' => $validated['waktu_belajar'],
                'status_kkm' => $validated['status_kkm'],
                'komite' => $validated['komite'],
                'jumlah_murid' => $validated['jumlah_murid'],
                'alamat_jalan' => $validated['alamat_jalan'] ?? null,
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'desa_kelurahan' => $validated['desa_kelurahan'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kabupaten_kota' => $validated['kabupaten_kota'] ?? null,
                'provinsi' => $validated['provinsi'] ?? null,
                'kodepos' => $validated['kodepos'] ?? null,
                'operator_name' => $validated['operator_name'],
                'operator_email' => $validated['operator_email'],
                'status' => $nextStatus,
                'admin_notes' => null,
                'rejected_at' => null,
            ], $pathUpdates))->save();

            $permitsByKey = $reg->permits()->get()->keyBy('permit_key');

            foreach (LembagaRegistration::permitDefinitions() as $order => $def) {
                $key = $def['key'];
                $row = is_array($permitsInput[$key] ?? null) ? $permitsInput[$key] : [];
                $nomor = isset($row['nomor_sk']) && $row['nomor_sk'] !== '' ? (string) $row['nomor_sk'] : null;
                $tanggal = isset($row['tanggal_sk']) && $row['tanggal_sk'] !== '' ? $row['tanggal_sk'] : null;

                /** @var LembagaRegistrationPermit|null $perm */
                $perm = $permitsByKey->get($key);

                if (! $perm) {
                    $dokPath = null;
                    if ($request->hasFile("permits.$key.dokumen")) {
                        $dokPath = $request->file("permits.$key.dokumen")->store($baseDir.'/perizinan', 'public');
                    }

                    LembagaRegistrationPermit::query()->create([
                        'lembaga_registration_id' => $reg->id,
                        'sort_order' => $order,
                        'permit_key' => $key,
                        'nama_sk' => $def['label'],
                        'nomor_sk' => $nomor,
                        'tanggal_sk' => $tanggal,
                        'dokumen_path' => $dokPath,
                    ]);

                    continue;
                }

                $dokPath = $perm->dokumen_path;
                if ($request->hasFile("permits.$key.dokumen")) {
                    if (is_string($dokPath) && $dokPath !== '') {
                        $disk->delete($dokPath);
                    }

                    $dokPath = $request->file("permits.$key.dokumen")->store($baseDir.'/perizinan', 'public');
                }

                $perm->forceFill([
                    'nomor_sk' => $nomor,
                    'tanggal_sk' => $tanggal,
                    'dokumen_path' => $dokPath,
                ])->save();
            }
        });

        $reg->refresh();

        if ($reg->status === LembagaRegistration::STATUS_AWAITING_MOU) {
            return redirect()
                ->route('public.lembaga-registrations.mou', ['token' => $reg->public_token])
                ->with('status', __('Data permohonan diperbarui. Silakan lanjutkan penandatanganan MoU.'));
        }

        return redirect()
            ->route('public.lembaga-registrations.status', ['token' => $reg->public_token])
            ->with('status', __('Data permohonan diperbarui dan dikirim ulang untuk verifikasi.'));
    }
}
