<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\PerpustakaanBuku;
use App\Models\PerpustakaanPeminjaman;
use App\Models\PerpustakaanPengaturan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PerpustakaanPeminjamanService
{
    /**
     * @return array{siswa_id: int|null, guru_id: int|null}
     */
    public function resolvePeminjamIds(User $user): array
    {
        if ($user->hasRole('siswa') && $user->siswa) {
            return ['siswa_id' => $user->siswa->id, 'guru_id' => null];
        }

        if ($user->hasRole('guru') && $user->guru) {
            return ['siswa_id' => null, 'guru_id' => $user->guru->id];
        }

        return ['siswa_id' => null, 'guru_id' => null];
    }

    public function pinjam(User $peminjam, PerpustakaanBuku $buku, string $tipePeminjaman, ?User $petugas = null): PerpustakaanPeminjaman
    {
        return DB::transaction(function () use ($peminjam, $buku, $tipePeminjaman, $petugas): PerpustakaanPeminjaman {
            $buku = PerpustakaanBuku::query()->lockForUpdate()->findOrFail($buku->id);
            $sekolahId = (int) $buku->sekolah_id;
            $pengaturan = PerpustakaanPengaturan::forSekolah($sekolahId);

            $this->assertCanPinjam($peminjam, $buku, $tipePeminjaman, $pengaturan);

            $ids = $this->resolvePeminjamIds($peminjam);
            $hari = $tipePeminjaman === 'digital'
                ? $pengaturan->masa_pinjam_digital_hari
                : $pengaturan->masa_pinjam_fisik_hari;

            if ($tipePeminjaman === 'fisik') {
                if ($buku->eksemplar_tersedia < 1) {
                    throw ValidationException::withMessages([
                        'tipe_peminjaman' => __('Eksemplar fisik tidak tersedia.'),
                    ]);
                }

                $buku->decrement('eksemplar_tersedia');
            }

            if ($tipePeminjaman === 'digital') {
                $sudahPinjam = PerpustakaanPeminjaman::query()
                    ->where('perpustakaan_buku_id', $buku->id)
                    ->where('user_id', $peminjam->id)
                    ->where('tipe_peminjaman', 'digital')
                    ->aktif()
                    ->exists();

                if ($sudahPinjam) {
                    throw ValidationException::withMessages([
                        'tipe_peminjaman' => __('Anda masih meminjam e-book ini.'),
                    ]);
                }
            }

            return PerpustakaanPeminjaman::query()->create([
                'sekolah_id' => $sekolahId,
                'perpustakaan_buku_id' => $buku->id,
                'user_id' => $peminjam->id,
                'siswa_id' => $ids['siswa_id'],
                'guru_id' => $ids['guru_id'],
                'tipe_peminjaman' => $tipePeminjaman,
                'status' => 'dipinjam',
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays($hari)->toDateString(),
                'diproses_oleh' => $petugas?->id ?? $peminjam->id,
            ]);
        });
    }

    public function kembalikan(PerpustakaanPeminjaman $peminjaman, ?User $petugas = null, ?string $catatan = null): PerpustakaanPeminjaman
    {
        return DB::transaction(function () use ($peminjaman, $petugas, $catatan): PerpustakaanPeminjaman {
            $peminjaman = PerpustakaanPeminjaman::query()->lockForUpdate()->findOrFail($peminjaman->id);

            if (! $peminjaman->isAktif()) {
                throw ValidationException::withMessages([
                    'status' => __('Peminjaman sudah selesai.'),
                ]);
            }

            $buku = PerpustakaanBuku::query()->lockForUpdate()->findOrFail($peminjaman->perpustakaan_buku_id);
            $pengaturan = PerpustakaanPengaturan::forSekolah((int) $peminjaman->sekolah_id);

            $denda = 0;
            if ($peminjaman->tipe_peminjaman === 'fisik' && now()->toDateString() > $peminjaman->tanggal_jatuh_tempo->toDateString()) {
                $hariTerlambat = $peminjaman->tanggal_jatuh_tempo->diffInDays(now());
                $denda = (int) ($hariTerlambat * $pengaturan->denda_per_hari);
            }

            if ($peminjaman->tipe_peminjaman === 'fisik') {
                $buku->increment('eksemplar_tersedia');
            }

            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => now()->toDateString(),
                'denda' => $denda,
                'catatan' => $catatan ?: $peminjaman->catatan,
                'diproses_oleh' => $petugas?->id ?? $peminjaman->diproses_oleh,
            ]);

            return $peminjaman->fresh();
        });
    }

    public function perpanjang(PerpustakaanPeminjaman $peminjaman, ?User $petugas = null): PerpustakaanPeminjaman
    {
        return DB::transaction(function () use ($peminjaman, $petugas): PerpustakaanPeminjaman {
            $peminjaman = PerpustakaanPeminjaman::query()->lockForUpdate()->findOrFail($peminjaman->id);
            $pengaturan = PerpustakaanPengaturan::forSekolah((int) $peminjaman->sekolah_id);

            if (! $peminjaman->isAktif()) {
                throw ValidationException::withMessages([
                    'status' => __('Peminjaman sudah selesai.'),
                ]);
            }

            if ($peminjaman->jumlah_perpanjangan >= $pengaturan->max_perpanjangan) {
                throw ValidationException::withMessages([
                    'perpanjangan' => __('Batas perpanjangan telah tercapai.'),
                ]);
            }

            $hari = $peminjaman->tipe_peminjaman === 'digital'
                ? $pengaturan->masa_pinjam_digital_hari
                : $pengaturan->masa_pinjam_fisik_hari;

            $peminjaman->update([
                'tanggal_jatuh_tempo' => $peminjaman->tanggal_jatuh_tempo->addDays($hari)->toDateString(),
                'jumlah_perpanjangan' => $peminjaman->jumlah_perpanjangan + 1,
                'diproses_oleh' => $petugas?->id ?? $peminjaman->diproses_oleh,
            ]);

            return $peminjaman->fresh();
        });
    }

    public function tandaiHilang(PerpustakaanPeminjaman $peminjaman, ?User $petugas = null, ?string $catatan = null): PerpustakaanPeminjaman
    {
        return DB::transaction(function () use ($peminjaman, $petugas, $catatan): PerpustakaanPeminjaman {
            $peminjaman = PerpustakaanPeminjaman::query()->lockForUpdate()->findOrFail($peminjaman->id);

            if (! $peminjaman->isAktif()) {
                throw ValidationException::withMessages([
                    'status' => __('Peminjaman sudah selesai.'),
                ]);
            }

            $peminjaman->update([
                'status' => 'hilang',
                'tanggal_kembali' => now()->toDateString(),
                'catatan' => $catatan ?: $peminjaman->catatan,
                'diproses_oleh' => $petugas?->id ?? $peminjaman->diproses_oleh,
            ]);

            return $peminjaman->fresh();
        });
    }

    private function assertCanPinjam(User $peminjam, PerpustakaanBuku $buku, string $tipePeminjaman, PerpustakaanPengaturan $pengaturan): void
    {
        if (! $buku->is_active) {
            throw ValidationException::withMessages(['buku' => __('Buku tidak aktif.')]);
        }

        if ($tipePeminjaman === 'fisik' && ! $buku->supportsFisik()) {
            throw ValidationException::withMessages(['tipe_peminjaman' => __('Buku ini tidak tersedia dalam format fisik.')]);
        }

        if ($tipePeminjaman === 'digital' && ! $buku->supportsDigital()) {
            throw ValidationException::withMessages(['tipe_peminjaman' => __('Buku digital / PDF belum tersedia.')]);
        }

        $aktifCount = PerpustakaanPeminjaman::query()
            ->where('user_id', $peminjam->id)
            ->aktif()
            ->count();

        if ($aktifCount >= $pengaturan->max_peminjaman_aktif) {
            throw ValidationException::withMessages([
                'peminjaman' => __('Batas peminjaman aktif (:max) telah tercapai.', ['max' => $pengaturan->max_peminjaman_aktif]),
            ]);
        }
    }
}
