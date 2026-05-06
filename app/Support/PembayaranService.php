<?php

namespace App\Support;

use App\Models\AkuntansiJurnal;
use App\Models\AkuntansiJurnalLine;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Carbon;

class PembayaranService
{
    public static function record(Tagihan $tagihan, array $data, int $userId): Pembayaran
    {
        $dibayarPada = ! empty($data['dibayar_pada'])
            ? Carbon::parse($data['dibayar_pada'], config('app.timezone'))
            : now();

        $pembayaran = $tagihan->pembayarans()->create([
            'jumlah' => $data['jumlah'],
            'metode' => $data['metode'],
            'referensi' => $data['referensi'] ?? null,
            'dibayar_pada' => $dibayarPada,
            'dicatat_oleh' => $userId,
        ]);

        $sekolahId = (int) $tagihan->sekolah_id;
        $akun = AkuntansiDefaults::ensureForSekolah($sekolahId);

        $jurnal = AkuntansiJurnal::query()->create([
            'sekolah_id' => $sekolahId,
            'tanggal' => $dibayarPada->toDateString(),
            'no_bukti' => $pembayaran->referensi ?: null,
            'keterangan' => __('Pembayaran tagihan :jenis (:periode) - :nama', [
                'jenis' => $tagihan->jenis,
                'periode' => $tagihan->periode,
                'nama' => $tagihan->siswa?->nama ?? '-',
            ]),
            'sumber_type' => Pembayaran::class,
            'sumber_id' => $pembayaran->id,
            'dibuat_oleh' => $userId,
        ]);

        AkuntansiJurnalLine::query()->create([
            'sekolah_id' => $sekolahId,
            'jurnal_id' => $jurnal->id,
            'akun_id' => $akun['kas']->id,
            'debit' => $pembayaran->jumlah,
            'kredit' => 0,
        ]);
        AkuntansiJurnalLine::query()->create([
            'sekolah_id' => $sekolahId,
            'jurnal_id' => $jurnal->id,
            'akun_id' => $akun['pendapatan']->id,
            'debit' => 0,
            'kredit' => $pembayaran->jumlah,
        ]);

        $pembayaran->forceFill(['akuntansi_jurnal_id' => $jurnal->id])->save();

        $tagihan->refresh();
        $tagihan->refreshStatus();

        return $pembayaran;
    }

    public static function deletePembayaranAndJurnal(Pembayaran $pembayaran): void
    {
        $tagihan = $pembayaran->tagihan;
        $jurnalId = $pembayaran->akuntansi_jurnal_id;

        $pembayaran->delete();
        if ($jurnalId) {
            AkuntansiJurnal::query()->whereKey($jurnalId)->delete();
        }

        $tagihan->refresh();
        $tagihan->refreshStatus();
    }
}

