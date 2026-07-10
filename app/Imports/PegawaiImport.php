<?php

namespace App\Imports;

use App\Imports\Concerns\ImportsGtkRows;
use App\Models\Pegawai;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PegawaiImport
{
    use ImportsGtkRows;

    public const SHEET_NAME = 'Tenaga Kependidikan';

    public const SHEET_INDEX = 1;

    public int $processed = 0;

    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    public function importFromFile(string $path): void
    {
        $this->collection(GtkSheetReader::rows($path, self::SHEET_NAME, self::SHEET_INDEX));
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $this->processed++;

            $nama = trim((string) ($row['nama_lengkap'] ?? ''));
            if ($nama === '') {
                $this->skipped++;

                continue;
            }

            $nip = $this->cleanId($row['nip'] ?? null) ?: null;
            $jabatan = $this->nullIfEmpty($row['tugas'] ?? null);
            $phone = $this->cleanPhone($row['nomor_handphone'] ?? null);
            $gtkProfile = $this->gtkProfileAttributes($row);
            $emailMadrasah = $this->nullIfEmpty(Str::lower(trim((string) ($row['email_akun_madrasah_hebat'] ?? ($row['email_akun_madrasah_digital'] ?? '')))));
            if ($emailMadrasah && empty($gtkProfile['email_pribadi'])) {
                $gtkProfile['email_pribadi'] = $emailMadrasah;
            }

            $attributes = array_merge([
                'nama' => $nama,
                'nip' => $nip,
                'jabatan' => $jabatan,
                'phone' => $phone,
                'is_active' => true,
            ], $gtkProfile);

            $existing = $this->findExistingPegawai($nip, $nama);

            if ($existing) {
                $existing->update($attributes);
                $this->updated++;

                continue;
            }

            Pegawai::query()->create($attributes);
            $this->created++;
        }
    }

    private function findExistingPegawai(?string $nip, string $nama): ?Pegawai
    {
        if ($nip !== null && $nip !== '') {
            $byNip = Pegawai::query()->where('nip', $nip)->first();
            if ($byNip) {
                return $byNip;
            }
        }

        return Pegawai::query()
            ->whereRaw('LOWER(nama) = ?', [Str::lower($nama)])
            ->first();
    }
}
