<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\Pegawai;
use App\Models\PresensiGuru;
use App\Models\PresensiPegawai;
use App\Models\PresensiSiswa;
use App\Models\Siswa;
use App\Support\FaceDescriptorMatcher;
use App\Support\SekolahPresensiSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PresensiScanService
{
    /** @var array<string, class-string<Model>> */
    private const PERSON_MODELS = [
        'siswa' => Siswa::class,
        'guru' => Guru::class,
        'pegawai' => Pegawai::class,
    ];

    /** @var array<string, class-string<Model>> */
    private const PRESENSI_MODELS = [
        'siswa' => PresensiSiswa::class,
        'guru' => PresensiGuru::class,
        'pegawai' => PresensiPegawai::class,
    ];

    /** @var array<string, string> */
    private const PERSON_KEYS = [
        'siswa' => 'siswa_id',
        'guru' => 'guru_id',
        'pegawai' => 'pegawai_id',
    ];

    /**
     * @return array{ok: bool, message: string, nama?: string, already?: bool, jam_masuk?: string|null}
     */
    public function recordBarcode(string $type, string $kode, ?string $tanggal = null, ?int $jadwalId = null): array
    {
        $person = $this->findPersonByKode($type, $kode);

        if (! $person) {
            return [
                'ok' => false,
                'message' => __('Kode barcode tidak dikenali.'),
            ];
        }

        if ($type === 'pegawai' && ! ($person->is_active ?? true)) {
            return [
                'ok' => false,
                'message' => __('Pegawai tidak aktif.'),
            ];
        }

        if ($type === 'siswa') {
            $validation = $this->validateSiswaJadwalContext($jadwalId, $tanggal, $person);
            if ($validation !== null) {
                return $validation;
            }
        }

        return $this->recordPresence($type, (int) $person->id, $person->nama, 'barcode', $tanggal, $jadwalId);
    }

    /**
     * @param  list<float>  $descriptor
     * @return array{ok: bool, message: string, nama?: string, already?: bool, jam_masuk?: string|null}
     */
    public function recordFace(string $type, array $descriptor, ?int $kelasId = null, ?string $tanggal = null, ?int $jadwalId = null): array
    {
        $match = $this->matchFace($type, $descriptor, $kelasId);

        if (! $match) {
            return [
                'ok' => false,
                'message' => __('Wajah tidak dikenali. Pastikan sudah didaftarkan atau coba lagi.'),
            ];
        }

        if ($type === 'pegawai') {
            $pegawai = Pegawai::query()->find($match['id']);
            if ($pegawai && ! $pegawai->is_active) {
                return [
                    'ok' => false,
                    'message' => __('Pegawai tidak aktif.'),
                ];
            }
        }

        if ($type === 'siswa') {
            $person = Siswa::query()->find($match['id']);
            $validation = $this->validateSiswaJadwalContext($jadwalId, $tanggal, $person);
            if ($validation !== null) {
                return $validation;
            }
        }

        return $this->recordPresence($type, $match['id'], $match['nama'], 'face', $tanggal, $jadwalId);
    }

    /**
     * @param  list<float>  $descriptor
     */
    public function enrollFace(string $type, int $personId, array $descriptor): bool
    {
        $person = $this->findPersonById($type, $personId);

        if (! $person) {
            return false;
        }

        $person->forceFill(['face_descriptor' => $descriptor])->save();

        return true;
    }

    public function findPersonByKode(string $type, string $kode): ?Model
    {
        $model = self::PERSON_MODELS[$type] ?? null;
        if (! $model) {
            return null;
        }

        $kode = trim($kode);

        return $model::query()->where('presensi_kode', $kode)->first();
    }

    /**
     * @param  list<float>  $descriptor
     * @return array{id: int, nama: string, distance: float}|null
     */
    public function matchFace(string $type, array $descriptor, ?int $kelasId = null): ?array
    {
        $model = self::PERSON_MODELS[$type] ?? null;
        if (! $model) {
            return null;
        }

        $query = $model::query()
            ->whereNotNull('face_descriptor')
            ->select(['id', 'nama', 'face_descriptor']);

        if ($type === 'siswa' && $kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($type === 'pegawai') {
            $query->where('is_active', true);
        }

        $candidates = $query->get()->map(fn (Model $row) => [
            'id' => (int) $row->id,
            'nama' => (string) $row->nama,
            'descriptor' => array_map('floatval', (array) $row->face_descriptor),
        ])->all();

        return FaceDescriptorMatcher::bestMatch($descriptor, $candidates);
    }

    /**
     * @return array{ok: bool, message: string, nama?: string, already?: bool, jam_masuk?: string|null}
     */
    private function recordPresence(string $type, int $personId, string $nama, string $metode, ?string $tanggal, ?int $jadwalId = null): array
    {
        $presensiModel = self::PRESENSI_MODELS[$type];
        $personKey = self::PERSON_KEYS[$type];
        $date = $tanggal ? Carbon::parse($tanggal)->toDateString() : now()->toDateString();
        $jam = now()->format('H:i:s');

        $lookup = [
            $personKey => $personId,
            'tanggal' => $date,
        ];
        $attributes = [
            'status' => 'hadir',
            'metode' => $metode,
            'jam_masuk' => $jam,
        ];

        if ($type === 'siswa') {
            $perMapel = SekolahPresensiSettings::isPerMapel();
            $slot = SekolahPresensiSettings::slotForJadwal($perMapel ? $jadwalId : null);
            $lookup['presensi_slot'] = $slot;
            $attributes['presensi_slot'] = $slot;
            $attributes['jadwal_id'] = $perMapel ? $jadwalId : null;
        }

        $existing = $presensiModel::query()->where($lookup)->first();

        if ($existing && $existing->status === 'hadir') {
            return [
                'ok' => true,
                'already' => true,
                'nama' => $nama,
                'jam_masuk' => $existing->jam_masuk,
                'message' => __(':nama sudah tercatat hadir.', ['nama' => $nama]),
            ];
        }

        $attributes['keterangan'] = $existing?->keterangan;

        $presensiModel::query()->updateOrCreate($lookup, $attributes);

        return [
            'ok' => true,
            'already' => false,
            'nama' => $nama,
            'jam_masuk' => $jam,
            'message' => __('Presensi :nama tercatat hadir.', ['nama' => $nama]),
        ];
    }

    /**
     * @return array{ok: false, message: string}|null
     */
    private function validateSiswaJadwalContext(?int $jadwalId, ?string $tanggal, ?Model $person): ?array
    {
        if (! SekolahPresensiSettings::isPerMapel()) {
            return null;
        }

        if (! $jadwalId) {
            return [
                'ok' => false,
                'message' => __('Pilih jadwal mapel terlebih dahulu (mode presensi per mapel).'),
            ];
        }

        if (! $person instanceof Siswa) {
            return [
                'ok' => false,
                'message' => __('Data siswa tidak valid.'),
            ];
        }

        $date = $tanggal ? Carbon::parse($tanggal)->toDateString() : now()->toDateString();

        $valid = \App\Models\Jadwal::query()
            ->whereKey($jadwalId)
            ->where('kelas_id', $person->kelas_id)
            ->where('hari', \App\Models\Jadwal::hariFromDate($date))
            ->exists();

        if (! $valid) {
            return [
                'ok' => false,
                'message' => __('Jadwal mapel tidak sesuai kelas siswa atau hari ini.'),
            ];
        }

        return null;
    }

    private function findPersonById(string $type, int $personId): ?Model
    {
        $model = self::PERSON_MODELS[$type] ?? null;
        if (! $model) {
            return null;
        }

        return $model::query()->find($personId);
    }

    public static function authorizeType(string $type): void
    {
        $map = [
            'siswa' => PresensiSiswa::class,
            'guru' => PresensiGuru::class,
            'pegawai' => PresensiPegawai::class,
        ];

        abort_unless(isset($map[$type]), 404);
        abort_unless(Auth::user()?->can('create', $map[$type]), 403);
    }
}
