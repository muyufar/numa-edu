<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #111; line-height: 1.5; }
        p { margin: 0 0 8px; text-align: justify; }
        .sig-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .sig-table td { width: 50%; vertical-align: top; text-align: center; padding: 14px 10px 8px; font-family: DejaVu Serif, serif; font-size: 12px; color: #111; }
        .sig-party { margin: 0 0 10px; }
        .sig-org { font-weight: bold; text-transform: uppercase; margin: 0 0 10px; line-height: 1.4; }
        .sig-meterai { font-size: 11px; margin: 0 0 36px; }
        .sig-space { min-height: 56px; }
        .sig-name { font-weight: bold; margin: 10px 0 0; text-transform: uppercase; line-height: 1.35; }
        .footer { margin-top: 14px; font-size: 10px; color: #555; }
        .mou-r26-kop-title { text-align: center; font-weight: bold; font-size: 15px; text-transform: uppercase; margin: 0 0 6px; line-height: 1.35; }
        .mou-r26-kop-sub { font-size: 11px; font-weight: normal; text-transform: none; color: #444; display: block; margin-top: 2px; }
        .mou-r26-center { text-align: center; }
        .mou-r26-small { color: #444; font-size: 12px; margin: 8px 0 4px; }
        .mou-r26-school { font-weight: bold; font-size: 14px; text-transform: uppercase; margin: 6px 0; text-align: center; }
        .mou-r26-lp-block { font-weight: bold; font-size: 12px; text-transform: uppercase; line-height: 1.45; margin: 8px 0; text-align: center; }
        .mou-r26-nomor { font-size: 12px; margin: 4px 0; }
        .mou-r26-pasal-h { text-transform: uppercase; font-weight: bold; text-align: center; margin: 14px 0 6px; font-size: 12px; }
        .mou-r26-ol { margin: 0 0 10px 22px; padding-left: 0; }
        .mou-r26-ol li { margin-bottom: 6px; text-align: justify; }
    </style>
</head>
<body>
    @include('partials.lembaga-mou-revisi-mar-body', [
        'reg' => $reg,
        'cabang' => $cabang,
        'nomorLp' => $nomorLp,
        'nomorSekolah' => $nomorSekolah,
        'mouCarbon' => $mouCarbon ?? now(),
    ])

    @php
        $cabSig = $cabang ?? $reg->cabang;
        $wilayahSig = trim((string) ($cabSig?->nama ?? ''));
        $lpPcnuSig = $wilayahSig !== '' ? "LP Ma'arif NU PCNU {$wilayahSig}" : "LP Ma'arif NU PCNU ………………";
        $namaLembagaSig = mb_strtoupper((string) $reg->nama_lembaga, 'UTF-8');
        $namaKsSig = $reg->nama_kepala !== null && trim((string) $reg->nama_kepala) !== ''
            ? mb_strtoupper(trim((string) $reg->nama_kepala), 'UTF-8')
            : '………………………………';
        $namaKetuaLpSig = $cabSig?->mou_penandatangan_nama !== null && trim((string) $cabSig->mou_penandatangan_nama) !== ''
            ? trim((string) $cabSig->mou_penandatangan_nama)
            : '………………………………';
    @endphp

    <p style="margin-top:10px;font-size:10px;color:#555;text-align:justify;">
        Catatan sistem Numa-Edu: meterai dan tanda tangan basah PIHAK KESATU dilengkapi pada salinan cetak; cap organisasi dan tanda tangan basah PIHAK KEDUA dilengkapi pada salinan cetak di kantor LP Ma'arif.
    </p>

    <table class="sig-table">
        <tr>
            <td>
                <p class="sig-party">PIHAK KESATU,</p>
                <p class="sig-org">{{ $namaLembagaSig }}</p>
                <p class="sig-meterai">(Materai 10.000)</p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $namaKsSig }}</p>
            </td>
            <td>
                <p class="sig-party">PIHAK KEDUA,</p>
                <p class="sig-org" style="text-transform:none;font-weight:bold;">{{ $lpPcnuSig }}</p>
                <div class="sig-space" style="margin-top:28px;"></div>
                <p class="sig-name" style="text-transform:none;">{{ $namaKetuaLpSig }}</p>
            </td>
        </tr>
    </table>

    <p class="footer">Dokumen ini dihasilkan otomatis oleh sistem Numa-Edu. Draft untuk verifikasi administrasi.</p>
</body>
</html>
