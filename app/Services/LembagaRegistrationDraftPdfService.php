<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\LembagaRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

final class LembagaRegistrationDraftPdfService
{
    public function generateDraftMouAndSertifikat(
        LembagaRegistration $reg,
        Cabang $cabang,
        string $nomorLp,
        string $nomorSekolah,
    ): void {
        $stampDataUrl = $cabang->mou_stempel_path ? $this->publicFileDataUrl($cabang->mou_stempel_path) : null;
        $ketuaTtdDataUrl = $cabang->mou_penandatangan_ttd_path ? $this->publicFileDataUrl($cabang->mou_penandatangan_ttd_path) : null;
        $kopHeaderDataUrl = $this->publicAssetDataUrl('images/lembaga/cert-kop-header.png');

        $tanggalSurat = now()->locale('id')->translatedFormat('d F Y');
        $tanggalIso = now()->format('Y-m-d');

        $mouPdf = Pdf::loadView('pdf.lembaga-mou-draft', [
            'reg' => $reg,
            'cabang' => $cabang,
            'nomorLp' => $nomorLp,
            'nomorSekolah' => $nomorSekolah,
            'tanggalSurat' => $tanggalSurat,
            'mouCarbon' => now(),
        ]);
        $mouPdf->setPaper('a4', 'portrait');

        $certPdf = Pdf::loadView('pdf.lembaga-e-sertifikat', [
            'reg' => $reg,
            'cabang' => $cabang,
            'nomorLp' => $nomorLp,
            'nomorSekolah' => $nomorSekolah,
            'tanggalSurat' => $tanggalSurat,
            'tanggalIso' => $tanggalIso,
            'stampDataUrl' => $stampDataUrl,
            'ketuaTtdDataUrl' => $ketuaTtdDataUrl,
            'kopHeaderDataUrl' => $kopHeaderDataUrl,
        ]);
        $certPdf->setPaper('a4', 'landscape');

        $baseDir = 'lembaga-registrations/'.$reg->public_token.'/pdf';
        $mouRel = $baseDir.'/draft_mou.pdf';
        $certRel = $baseDir.'/e_sertifikat.pdf';

        Storage::disk('public')->put($mouRel, $mouPdf->output());
        Storage::disk('public')->put($certRel, $certPdf->output());

        $reg->forceFill([
            'mou_draft_pdf_path' => $mouRel,
            'e_sertifikat_pdf_path' => $certRel,
        ])->save();
    }

    private function publicFileDataUrl(?string $relative): ?string
    {
        if ($relative === null || $relative === '' || ! Storage::disk('public')->exists($relative)) {
            return null;
        }

        $bin = Storage::disk('public')->get($relative);
        $ext = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };

        return 'data:'.$mime.';base64,'.base64_encode($bin);
    }

    private function publicAssetDataUrl(string $relativeFromPublic): ?string
    {
        $path = public_path($relativeFromPublic);
        if (! is_file($path)) {
            return null;
        }

        $bin = file_get_contents($path);
        if ($bin === false || $bin === '') {
            return null;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };

        return 'data:'.$mime.';base64,'.base64_encode($bin);
    }
}
