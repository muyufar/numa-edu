<?php

namespace App\Imports\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

trait ImportsGtkRows
{
    protected function normalizeJenisKelamin(mixed $value): ?string
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

    protected function cleanId(mixed $value): string
    {
        $v = trim((string) ($value ?? ''));
        $v = ltrim($v, "'");

        return trim($v);
    }

    protected function cleanPhone(mixed $value): ?string
    {
        $v = $this->cleanId($value);
        if ($v === '' || Str::lower($v) === 'tidak ada') {
            return null;
        }

        return $v;
    }

    protected function nullIfEmpty(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        if ($v === '' || Str::lower($v) === 'tidak ada') {
            return null;
        }

        return $v;
    }

    protected function gtkProfileAttributes(Collection|array $row): array
    {
        return array_filter([
            'nik' => $this->cleanId($row['nik'] ?? null) ?: null,
            'nuptk' => $this->cleanId($row['nuptk'] ?? null) ?: null,
            'status_kepegawaian' => $this->nullIfEmpty((string) ($row['status_kepegawaian'] ?? '')),
            'tempat_lahir' => $this->nullIfEmpty((string) ($row['tempat_lahir'] ?? '')),
            'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? null),
            'email_pribadi' => $this->nullIfEmpty(Str::lower(trim((string) ($row['email'] ?? '')))),
            'jenis_kelamin' => $this->normalizeJenisKelamin($row['jenis_kelamin'] ?? null),
        ], fn ($value) => $value !== null);
    }

    protected function parseDate(mixed $value): ?string
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
}
