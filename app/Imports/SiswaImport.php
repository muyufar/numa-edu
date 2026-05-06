<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SiswaImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $processed = 0;
    public int $created = 0;
    public int $updated = 0;

    public function collection(Collection $rows): void
    {
        $kelasMap = [];

        // Build lookup map for "Kelas" column (by id and by label).
        $kelasById = Kelas::query()->get(['id', 'tingkat', 'nama', 'tahun_ajaran']);
        foreach ($kelasById as $k) {
            $kelasMap[(string) $k->id] = (int) $k->id;

            $fullLabel = trim("{$k->tingkat} {$k->nama} {$k->tahun_ajaran}");
            $kelasMap[$this->normalize($fullLabel)] = (int) $k->id;

            $kelasMap[$this->normalize((string) $k->tingkat.' '.$k->nama)] = (int) $k->id;
            $kelasMap[$this->normalize((string) $k->nama)] = (int) $k->id;
            $kelasMap[$this->normalize((string) $k->tingkat)] = (int) $k->id;
        }

        foreach ($rows as $row) {
            $this->processed++;

            // Keys come from HeadingRowFormatter::slug()
            $nisn = $this->cleanId($row['nisn'] ?? null);
            $namaLengkap = trim((string) ($row['nama_lengkap'] ?? ''));

            if ($nisn === '' || $namaLengkap === '') {
                continue;
            }

            $tingkatRombel = trim((string) ($row['tingkat_rombel'] ?? ''));
            $kelasId = $this->resolveKelasIdFromTingkatRombel($tingkatRombel, $kelasMap);

            $tanggalLahir = $this->parseTanggalLahir($row['tanggal_lahir'] ?? null);

            $jenisKelamin = $this->normalizeJenisKelamin($row['jenis_kelamin'] ?? null);

            $alamat = trim((string) ($row['alamat'] ?? ''));

            $attributes = [
                'nis' => $nisn,
                'nisn' => $nisn,
                'nama' => $namaLengkap,
                'nik' => $this->cleanId($row['nik'] ?? null) ?: null,
                'tempat_lahir' => $this->nullIfEmpty((string) ($row['tempat_lahir'] ?? '')),
                'tanggal_lahir' => $tanggalLahir,
                'tingkat_rombel' => $this->nullIfEmpty($tingkatRombel),
                'umur' => $this->nullIfEmpty((string) ($row['umur'] ?? '')),
                'status' => $this->nullIfEmpty((string) ($row['status'] ?? '')),
                'jenis_kelamin' => $jenisKelamin,
                'alamat' => $alamat !== '' ? $alamat : null,
                'no_telepon' => $this->cleanPhone($row['no_telepon'] ?? null),
                'kebutuhan_khusus' => $this->nullIfEmpty((string) ($row['kebutuhan_khusus'] ?? '')),
                'disabilitas' => $this->nullIfEmpty((string) ($row['disabilitas'] ?? '')),
                'nomor_kip_pip' => $this->nullIfEmpty((string) ($row['nomor_kip_pip'] ?? '')),
                'nama_ayah_kandung' => $this->nullIfEmpty((string) ($row['nama_ayah_kandung'] ?? '')),
                'nama_ibu_kandung' => $this->nullIfEmpty((string) ($row['nama_ibu_kandung'] ?? '')),
                'nama_wali' => $this->nullIfEmpty((string) ($row['nama_wali'] ?? '')),
            ];

            // Only update class placement when we can resolve it.
            if ($kelasId !== null) {
                $attributes['kelas_id'] = $kelasId;
            }

            $existing = Siswa::query()->where('nis', $nisn)->first();
            if ($existing) {
                $existing->update($attributes);
                $this->updated++;
                continue;
            }

            Siswa::query()->create($attributes);
            $this->created++;
        }
    }

    private function resolveKelasIdFromTingkatRombel(string $tingkatRombel, array $kelasMap): ?int
    {
        $raw = trim($tingkatRombel);
        if ($raw === '') {
            return null;
        }

        // Often looks like "Kelas 9 - KELAS 9A"
        $parts = array_map('trim', explode('-', $raw));
        $candidate = count($parts) >= 2 ? end($parts) : $raw;

        $key = $this->normalize((string) $candidate);

        return $kelasMap[$key] ?? null;
    }

    private function normalize(string|null $value): string
    {
        return Str::of($value ?? '')
            ->lower()
            ->replaceMatches('/\s+/u', ' ')
            ->trim()
            ->toString();
    }

    private function parseTanggalLahir(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }

            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeJenisKelamin(mixed $value): ?string
    {
        $v = Str::of((string) ($value ?? ''))->trim()->toString();
        if ($v === '') {
            return null;
        }

        $u = Str::upper($v);
        $u = Str::of($u)->replace(['-', '_'], ' ')->replaceMatches('/\s+/u', ' ')->trim()->toString();

        if (in_array($u, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'LAKI'], true)) {
            return 'L';
        }

        if (in_array($u, ['P', 'PEREMPUAN', 'PEREM'], true)) {
            return 'P';
        }

        return null;
    }

    private function nullIfEmpty(string $value): ?string
    {
        $v = trim($value);
        if ($v === '' || Str::lower($v) === 'tidak ada') {
            return null;
        }

        return $v;
    }

    private function cleanId(mixed $value): string
    {
        $v = trim((string) ($value ?? ''));
        $v = ltrim($v, "'");

        return trim($v);
    }

    private function cleanPhone(mixed $value): ?string
    {
        $v = $this->cleanId($value);
        if ($v === '' || Str::lower($v) === 'tidak ada') {
            return null;
        }

        return $v;
    }
}

