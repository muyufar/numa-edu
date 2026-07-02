<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Sekolah;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfDocument;

final class KeuanganPdfService
{
    public function tagihanInvoice(Tagihan $tagihan): DomPdfDocument
    {
        $tagihan->load([
            'siswa.kelas:id,tingkat,nama,tahun_ajaran',
            'pembayarans' => fn ($q) => $q->orderBy('dibayar_pada')->orderBy('id'),
        ]);

        $sekolah = Sekolah::withoutGlobalScopes()->find($tagihan->sekolah_id);
        $sisa = $tagihan->sisa();

        $pdf = Pdf::loadView('pdf.tagihan-invoice', [
            'tagihan' => $tagihan,
            'sekolah' => $sekolah,
            'sisa' => $sisa,
            'nomorInvoice' => $this->nomorInvoice($tagihan),
            'tanggalCetak' => now()->locale('id')->translatedFormat('d F Y'),
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    public function kwitansiPembayaran(Pembayaran $pembayaran): DomPdfDocument
    {
        $pembayaran->load([
            'tagihan.siswa.kelas:id,tingkat,nama,tahun_ajaran',
            'dicatatOleh:id,name',
        ]);

        $tagihan = $pembayaran->tagihan;
        $sekolah = Sekolah::withoutGlobalScopes()->find($pembayaran->sekolah_id);

        $pdf = Pdf::loadView('pdf.kwitansi-pembayaran', [
            'pembayaran' => $pembayaran,
            'tagihan' => $tagihan,
            'sekolah' => $sekolah,
            'nomorKwitansi' => $this->nomorKwitansi($pembayaran),
            'tanggalCetak' => now()->locale('id')->translatedFormat('d F Y'),
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    public function nomorInvoice(Tagihan $tagihan): string
    {
        return sprintf('INV-%d-%s', $tagihan->id, str_replace('-', '', $tagihan->periode));
    }

    public function nomorKwitansi(Pembayaran $pembayaran): string
    {
        $year = $pembayaran->dibayar_pada?->format('Y') ?? now()->format('Y');

        return sprintf('KW-%d-%s', $pembayaran->id, $year);
    }
}
