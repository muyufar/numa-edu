<?php

namespace App\Imports;

use App\Imports\Concerns\ImportsGtkRows;
use App\Models\Guru;
use App\Models\Scopes\TenantScope;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use ImportsGtkRows;

    public const SHEET_NAME = 'Guru';

    public const SHEET_INDEX = 0;

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
        $sekolahId = TenantScope::effectiveSekolahId();
        if ($sekolahId === false || $sekolahId === null) {
            $sekolahId = (int) config('tenancy.default_sekolah_id', 1);
        }

        foreach ($rows as $row) {
            $this->processed++;

            $nama = trim((string) ($row['nama_lengkap'] ?? ''));
            if ($nama === '') {
                $this->skipped++;

                continue;
            }

            $nip = $this->cleanId($row['nip'] ?? null) ?: null;
            $phone = $this->cleanPhone($row['nomor_handphone'] ?? null);
            $jenisKelamin = $this->normalizeJenisKelamin($row['jenis_kelamin'] ?? null);
            $email = $this->resolveEmail($row);
            $password = trim((string) ($row['password_awal'] ?? ''));

            if ($email === '') {
                $this->skipped++;

                continue;
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->skipped++;

                continue;
            }

            $gtkProfile = $this->gtkProfileAttributes($row);
            $gtkGuru = array_filter([
                'tugas' => $this->nullIfEmpty((string) ($row['tugas'] ?? '')),
                'mata_pelajaran' => $this->nullIfEmpty((string) ($row['mata_pelajaran'] ?? '')),
                'penempatan' => $this->nullIfEmpty((string) ($row['penempatan'] ?? '')),
                'total_jtm' => $this->nullIfEmpty((string) ($row['total_jtm'] ?? '')),
            ], fn ($value) => $value !== null);

            $existing = $this->findExistingGuru($nip, $email);

            if ($existing) {
                $this->updateGuru($existing, $nama, $nip, $phone, $jenisKelamin, $email, $password, array_merge($gtkProfile, $gtkGuru));
                $this->updated++;

                continue;
            }

            $this->createGuru((int) $sekolahId, $nama, $nip, $phone, $jenisKelamin, $email, $password, array_merge($gtkProfile, $gtkGuru));
            $this->created++;
        }
    }

    private function resolveEmail(Collection|array $row): string
    {
        foreach ([
            'email_akun_madrasah_digital',
            'email_akun_madrasah_hebat',
            'email',
        ] as $key) {
            $email = Str::lower(trim((string) ($row[$key] ?? '')));
            if ($email !== '') {
                return $email;
            }
        }

        $nuptk = $this->cleanId($row['nuptk'] ?? null);
        if ($nuptk !== '') {
            return $nuptk.'@numaedu.id';
        }

        $nip = $this->cleanId($row['nip'] ?? null);
        if ($nip !== '') {
            return $nip.'@numaedu.id';
        }

        return '';
    }

    private function findExistingGuru(?string $nip, string $email): ?Guru
    {
        if ($nip !== null && $nip !== '') {
            $byNip = Guru::query()->where('nip', $nip)->first();
            if ($byNip) {
                return $byNip;
            }
        }

        return Guru::query()
            ->whereHas('user', fn ($query) => $query->where('email', $email))
            ->first();
    }

    private function createGuru(
        int $sekolahId,
        string $nama,
        ?string $nip,
        ?string $phone,
        ?string $jenisKelamin,
        string $email,
        string $password,
        array $gtkAttributes = [],
    ): void {
        DB::transaction(function () use ($sekolahId, $nama, $nip, $phone, $jenisKelamin, $email, $password, $gtkAttributes): void {
            $user = User::query()->create([
                'name' => $nama,
                'email' => $email,
                'password' => $password !== '' ? $password : Str::password(12),
                'sekolah_id' => $sekolahId,
                'email_verified_at' => now(),
            ]);

            $user->assignRole('guru');

            Guru::query()->create(array_merge([
                'user_id' => $user->id,
                'nama' => $nama,
                'nip' => $nip,
                'phone' => $phone,
                'jenis_kelamin' => $jenisKelamin,
            ], $gtkAttributes));
        });
    }

    private function updateGuru(
        Guru $guru,
        string $nama,
        ?string $nip,
        ?string $phone,
        ?string $jenisKelamin,
        string $email,
        string $password,
        array $gtkAttributes = [],
    ): void {
        DB::transaction(function () use ($guru, $nama, $nip, $phone, $jenisKelamin, $email, $password, $gtkAttributes): void {
            $userPayload = [
                'name' => $nama,
                'email' => $email,
            ];

            if ($password !== '') {
                $userPayload['password'] = $password;
            }

            $guru->user->update($userPayload);

            if (! $guru->user->hasRole('guru')) {
                $guru->user->assignRole('guru');
            }

            $guru->update(array_merge([
                'nama' => $nama,
                'nip' => $nip,
                'phone' => $phone,
                'jenis_kelamin' => $jenisKelamin,
            ], $gtkAttributes));
        });
    }
}
