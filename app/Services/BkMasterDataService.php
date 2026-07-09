<?php

namespace App\Services;

use App\Models\BkJenisPelanggaran;
use App\Models\BkSanksi;
use App\Models\Sekolah;
use App\Support\BkTingkat;

class BkMasterDataService
{
    public function ensureForSekolah(int $sekolahId): void
    {
        if (BkJenisPelanggaran::withoutGlobalScopes()->where('sekolah_id', $sekolahId)->exists()) {
            return;
        }

        $defaults = [
            ['kode' => 'terlambat', 'nama' => 'Keterlambatan', 'poin' => 2, 'tingkat' => 'ringan'],
            ['kode' => 'seragam', 'nama' => 'Pelanggaran seragam', 'poin' => 3, 'tingkat' => 'ringan'],
            ['kode' => 'atribut', 'nama' => 'Atribut / kelengkapan', 'poin' => 2, 'tingkat' => 'ringan'],
            ['kode' => 'kelakuan', 'nama' => 'Kelakuan di kelas', 'poin' => 5, 'tingkat' => 'sedang'],
            ['kode' => 'perundungan', 'nama' => 'Perundungan / konflik', 'poin' => 10, 'tingkat' => 'berat'],
            ['kode' => 'hp', 'nama' => 'Penggunaan ponsel', 'poin' => 3, 'tingkat' => 'ringan'],
            ['kode' => 'tugas', 'nama' => 'Ketidakhadiran tugas', 'poin' => 2, 'tingkat' => 'ringan'],
            ['kode' => 'lainnya', 'nama' => 'Lainnya', 'poin' => 1, 'tingkat' => 'ringan'],
        ];

        foreach ($defaults as $row) {
            BkJenisPelanggaran::withoutGlobalScopes()->create([
                'sekolah_id' => $sekolahId,
                ...$row,
                'is_active' => true,
            ]);
        }

        $sanksis = [
            ['nama' => 'Pembinaan lisan', 'tingkat' => 'ringan', 'deskripsi' => 'Teguran dan pembinaan langsung'],
            ['nama' => 'Surat peringatan 1', 'tingkat' => 'sedang', 'deskripsi' => 'Peringatan tertulis pertama'],
            ['nama' => 'Surat peringatan 2', 'tingkat' => 'sedang', 'deskripsi' => 'Peringatan tertulis kedua'],
            ['nama' => 'Skorsing singkat', 'tingkat' => 'berat', 'deskripsi' => 'Penghentian sementara kegiatan belajar'],
            ['nama' => 'Pemanggilan wali', 'tingkat' => 'berat', 'deskripsi' => 'Memanggil wali murid ke sekolah'],
            ['nama' => 'Home visit', 'tingkat' => 'berat', 'deskripsi' => 'Kunjungan rumah oleh tim BK'],
        ];

        foreach ($sanksis as $row) {
            BkSanksi::withoutGlobalScopes()->create([
                'sekolah_id' => $sekolahId,
                ...$row,
                'is_active' => true,
            ]);
        }
    }

    public function ensureForCurrentTenant(): void
    {
        $sekolahId = Sekolah::query()->value('id') ?? (int) config('tenancy.default_sekolah_id', 1);
        $this->ensureForSekolah((int) $sekolahId);
    }
}
