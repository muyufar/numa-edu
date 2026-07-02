<?php

namespace App\Support;

use App\Models\AkuntansiAkun;
use App\Models\AkuntansiJurnal;
use App\Models\AkuntansiJurnalLine;
use App\Models\PemasukanKas;
use Illuminate\Support\Facades\DB;

class PemasukanKasService
{
    /**
     * @param  array{tanggal: string, jumlah: float|int|string, keterangan: string, no_bukti?: ?string, bukti_nota_path?: ?string, akun_pendapatan_id?: ?int}  $data
     */
    public static function create(int $sekolahId, int $userId, array $data): PemasukanKas
    {
        return DB::transaction(function () use ($sekolahId, $userId, $data): PemasukanKas {
            $akun = AkuntansiDefaults::ensureForSekolah($sekolahId);
            $kas = $akun['kas'];

            $pendapatanAkun = $akun['pendapatan'];
            if (! empty($data['akun_pendapatan_id'])) {
                $picked = AkuntansiAkun::query()
                    ->where('sekolah_id', $sekolahId)
                    ->where('tipe', 'pendapatan')
                    ->where('is_active', true)
                    ->whereKey($data['akun_pendapatan_id'])
                    ->firstOrFail();
                $pendapatanAkun = $picked;
            }

            $pemasukan = PemasukanKas::query()->create([
                'sekolah_id' => $sekolahId,
                'tanggal' => $data['tanggal'],
                'jumlah' => $data['jumlah'],
                'keterangan' => $data['keterangan'],
                'no_bukti' => $data['no_bukti'] ?? null,
                'bukti_nota_path' => $data['bukti_nota_path'] ?? null,
                'akun_pendapatan_id' => $pendapatanAkun->id,
                'dibuat_oleh' => $userId,
            ]);

            $jumlah = (string) $pemasukan->jumlah;

            $jurnal = AkuntansiJurnal::query()->create([
                'sekolah_id' => $sekolahId,
                'tanggal' => $data['tanggal'],
                'no_bukti' => $pemasukan->no_bukti,
                'keterangan' => __('Pemasukan kas: :ket', ['ket' => $pemasukan->keterangan]),
                'sumber_type' => PemasukanKas::class,
                'sumber_id' => $pemasukan->id,
                'dibuat_oleh' => $userId,
            ]);

            AkuntansiJurnalLine::query()->create([
                'sekolah_id' => $sekolahId,
                'jurnal_id' => $jurnal->id,
                'akun_id' => $kas->id,
                'debit' => $jumlah,
                'kredit' => 0,
            ]);
            AkuntansiJurnalLine::query()->create([
                'sekolah_id' => $sekolahId,
                'jurnal_id' => $jurnal->id,
                'akun_id' => $pendapatanAkun->id,
                'debit' => 0,
                'kredit' => $jumlah,
            ]);

            $pemasukan->forceFill(['akuntansi_jurnal_id' => $jurnal->id])->save();

            return $pemasukan->fresh(['jurnal.lines.akun', 'akunPendapatan']);
        });
    }

    public static function destroyWithJurnal(PemasukanKas $pemasukan): void
    {
        DB::transaction(function () use ($pemasukan): void {
            $jid = $pemasukan->akuntansi_jurnal_id;
            if ($jid) {
                AkuntansiJurnal::query()->whereKey($jid)->delete();
            }
            KeuanganBuktiNotaStorage::delete($pemasukan->bukti_nota_path);
            $pemasukan->delete();
        });
    }
}
