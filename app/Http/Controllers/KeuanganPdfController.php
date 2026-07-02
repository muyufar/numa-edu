<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Services\KeuanganPdfService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class KeuanganPdfController extends Controller
{
    public function __construct(
        private readonly KeuanganPdfService $pdfService,
    ) {}

    public function tagihanInvoice(Tagihan $tagihan): Response
    {
        Gate::authorize('view', $tagihan);

        $pdf = $this->pdfService->tagihanInvoice($tagihan);
        $filename = $this->pdfService->nomorInvoice($tagihan).'.pdf';

        return $pdf->download($filename);
    }

    public function kwitansiPembayaran(Pembayaran $pembayaran): Response
    {
        Gate::authorize('view', $pembayaran);

        $pdf = $this->pdfService->kwitansiPembayaran($pembayaran);
        $filename = $this->pdfService->nomorKwitansi($pembayaran).'.pdf';

        return $pdf->download($filename);
    }
}
