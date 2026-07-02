<?php

namespace App\Support;

use App\Models\AkuntansiJurnal;
use App\Models\AkuntansiJurnalLine;
use App\Models\AkuntansiAkun;
use App\Models\PengeluaranKas;
use Illuminate\Support\Facades\DB;

class PengeluaranKasService
{
    /**
     * @param  array{tanggal: string, jumlah: float|int|string, keterangan: string, no_bukti?: ?string, bukti_nota_path?: ?string, akun_beban_id?: ?int}  $data
     */
    public static function create(int $sekolahId, int $userId, array $data): PengeluaranKas
    {
        return DB::transaction(function () use ($sekolahId, $userId, $data): PengeluaranKas {
            $akun = AkuntansiDefaults::ensureForSekolah($sekolahId);
            $kas = $akun['kas'];

            $bebanAkun = $akun['beban'];
            if (! empty($data['akun_beban_id'])) {
                $picked = AkuntansiAkun::query()
                    ->where('sekolah_id', $sekolahId)
                    ->where('tipe', 'beban')
                    ->where('is_active', true)
                    ->whereKey($data['akun_beban_id'])
                    ->firstOrFail();
                $bebanAkun = $picked;
            }

            $pengeluaran = PengeluaranKas::query()->create([
                'sekolah_id' => $sekolahId,
                'tanggal' => $data['tanggal'],
                'jumlah' => $data['jumlah'],
                'keterangan' => $data['keterangan'],
                'no_bukti' => $data['no_bukti'] ?? null,
                'bukti_nota_path' => $data['bukti_nota_path'] ?? null,
                'akun_beban_id' => $bebanAkun->id,
                'dibuat_oleh' => $userId,
            ]);

            $jumlah = (string) $pengeluaran->jumlah;

            $jurnal = AkuntansiJurnal::query()->create([
                'sekolah_id' => $sekolahId,
                'tanggal' => $data['tanggal'],
                'no_bukti' => $pengeluaran->no_bukti,
                'keterangan' => __('Pengeluaran kas: :ket', ['ket' => $pengeluaran->keterangan]),
                'sumber_type' => PengeluaranKas::class,
                'sumber_id' => $pengeluaran->id,
                'dibuat_oleh' => $userId,
            ]);

            AkuntansiJurnalLine::query()->create([
                'sekolah_id' => $sekolahId,
                'jurnal_id' => $jurnal->id,
                'akun_id' => $bebanAkun->id,
                'debit' => $jumlah,
                'kredit' => 0,
            ]);
            AkuntansiJurnalLine::query()->create([
                'sekolah_id' => $sekolahId,
                'jurnal_id' => $jurnal->id,
                'akun_id' => $kas->id,
                'debit' => 0,
                'kredit' => $jumlah,
            ]);

            $pengeluaran->forceFill(['akuntansi_jurnal_id' => $jurnal->id])->save();

            return $pengeluaran->fresh(['jurnal.lines.akun', 'akunBeban']);
        });
    }

    public static function destroyWithJurnal(PengeluaranKas $pengeluaran): void
    {
        DB::transaction(function () use ($pengeluaran): void {
            $jid = $pengeluaran->akuntansi_jurnal_id;
            if ($jid) {
                AkuntansiJurnal::query()->whereKey($jid)->delete();
            }
            KeuanganBuktiNotaStorage::delete($pengeluaran->bukti_nota_path);
            $pengeluaran->delete();
        });
    }
}
