<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.45; margin: 0; }
        p { margin: 0 0 6px; }
        .cert-outer {
            border: 3px solid #1a2f5c;
            padding: 3px;
            min-height: 500px;
            box-sizing: border-box;
            background: #fff;
        }
        .cert-inner {
            border: 1px solid #c5a028;
            padding: 16px 28px 20px;
            min-height: 492px;
            box-sizing: border-box;
            position: relative;
            max-width: 820px;
            margin: 0 auto;
            overflow: hidden;
            background-color: #fffefc;
        }
        .cert-main { position: relative; z-index: 2; }
        .cert-kop-img-wrap { text-align: center; margin: 0 0 10px; }
        .cert-kop-img { max-height: 88px; max-width: 100%; width: auto; }
        .cert-kop-fallback {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #1a2f5c;
            line-height: 1.35;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .cert-kop-fallback-sub { margin-top: 4px; font-size: 8.5px; color: #234; }
        .cert-rule { height: 2px; background: #c5a028; margin: 8px auto 12px; width: 88%; }
        .cert-label { text-align: center; font-size: 11px; margin-top: 4px; }
        .cert-recipient {
            text-align: center;
            font-family: DejaVu Serif, serif;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #111;
            margin: 6px 0 12px;
            line-height: 1.25;
            word-wrap: break-word;
        }
        .cert-npsn-wrap { text-align: center; margin-bottom: 14px; }
        .cert-npsn-label { font-size: 10px; color: #333; }
        .cert-npsn-val {
            font-family: DejaVu Sans, monospace;
            font-size: 16px;
            font-weight: bold;
            color: #1a2f5c;
            margin-top: 2px;
        }
        .cert-body {
            text-align: center;
            font-size: 10.5px;
            line-height: 1.55;
            padding: 0 12px 8px;
            color: #222;
        }
        .cert-issued { text-align: center; font-size: 10.5px; margin: 16px 0 8px; color: #111; }
        .ttd-wrap { margin-top: 6px; width: 100%; text-align: center; }
        .ttd-inner { display: inline-block; text-align: center; min-width: 240px; max-width: 300px; }
        .ttd-combo-single { text-align: center; min-height: 128px; padding: 6px 0 4px; }
        .ttd-combo-empty { text-align: center; min-height: 88px; padding-top: 8px; }
        .placeholder-stamp {
            display: inline-block; width: 100px; height: 100px; border: 2px dashed #94a3b8; border-radius: 50%;
            font-size: 7px; color: #64748b; line-height: 1.2; padding-top: 34px; vertical-align: middle;
        }
        .sig-placeholder {
            display: inline-block; min-width: 120px; min-height: 44px; border-bottom: 1px solid #cbd5e1; margin: 8px 0 0;
        }
        .cert-sign-name {
            font-size: 12px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 4px;
            color: #111;
        }
        .cert-sign-title { font-size: 9.5px; color: #222; margin-top: 5px; line-height: 1.35; }
        .foot {
            position: absolute;
            bottom: 10px;
            left: 28px;
            right: 28px;
            font-size: 7.5px;
            color: #555;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            z-index: 4;
        }
    </style>
</head>
<body>
    @php
        $wilayah = trim((string) ($cabang->nama ?? ''));
        $wilayahTanpaPcnuPrefiks = $wilayah !== '' ? (string) preg_replace('/^PCNU\s+/iu', '', $wilayah) : '';
        $kopLpReadable = $wilayah !== '' ? "LP Ma'arif NU PCNU {$wilayahTanpaPcnuPrefiks}" : "LP Ma'arif NU PCNU ………………";
        $kopLpUpper = $wilayah !== '' ? 'LP MA\'ARIF NU PCNU '.mb_strtoupper($wilayah, 'UTF-8') : "LP MA'ARIF NU PCNU ………………";
        $namaSekolahCert = mb_strtoupper((string) $reg->nama_lembaga, 'UTF-8');
        $namaKetua = $cabang->mou_penandatangan_nama ?? '………………………………';
        $jabatanKetua = $cabang->mou_penandatangan_jabatan ?? "Ketua LP Ma'arif NU PCNU\nKabupaten/Kota";
        $kotaSurat = $cabang->mou_surat_kota ?? '………………';
        $hasStampUrl = ! empty($stampDataUrl);
        $hasSigUrl = ! empty($ketuaTtdDataUrl);
        $hasKopImg = ! empty($kopHeaderDataUrl);

        $svgCornerTr = '<svg xmlns="http://www.w3.org/2000/svg" width="118" height="118"><defs><linearGradient id="trg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#d4af37"/><stop offset="0.45" stop-color="#2a5580"/><stop offset="1" stop-color="#0f1f3d"/></linearGradient></defs><polygon points="118,0 118,118 0,0" fill="url(#trg)"/></svg>';
        $svgCornerBl = '<svg xmlns="http://www.w3.org/2000/svg" width="118" height="118"><defs><linearGradient id="blg" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#d4af37"/><stop offset="0.45" stop-color="#2a5580"/><stop offset="1" stop-color="#0f1f3d"/></linearGradient></defs><polygon points="0,118 118,118 0,0" fill="url(#blg)"/></svg>';
        $cornerTrSrc = 'data:image/svg+xml;base64,'.base64_encode($svgCornerTr);
        $cornerBlSrc = 'data:image/svg+xml;base64,'.base64_encode($svgCornerBl);
    @endphp

    <div class="cert-outer">
        <div class="cert-inner">
            <img src="{{ $cornerTrSrc }}" width="118" height="118" alt="" style="position:absolute;top:0;right:0;z-index:1;display:block;">
            <img src="{{ $cornerBlSrc }}" width="118" height="118" alt="" style="position:absolute;bottom:0;left:0;z-index:1;display:block;">

            <div class="cert-main">
                @if ($hasKopImg)
                    <div class="cert-kop-img-wrap">
                        <img class="cert-kop-img" src="{{ $kopHeaderDataUrl }}" alt="Kop sertifikat">
                    </div>
                @else
                    <div class="cert-kop-fallback">
                        LEMBAGA PENDIDIKAN MA'ARIF<br>NAHDLATUL ULAMA
                        <div class="cert-kop-fallback-sub">{{ $kopLpUpper }}</div>
                    </div>
                @endif
                <div class="cert-rule"></div>
                <div style="text-align:center;margin:8px 0 14px;">
                    <div style="font-family:DejaVu Serif,DejaVu Sans,sans-serif;font-size:46px;font-weight:bold;letter-spacing:5px;color:#0d3d26;line-height:1.05;">SERTIFIKAT</div>
                    <table align="center" cellpadding="0" cellspacing="0" style="margin-top:6px;border-collapse:collapse;width:72%;max-width:420px;">
                        <tr>
                            <td style="height:5px;width:34%;background-color:#c9a227;"></td>
                            <td style="height:5px;width:33%;background-color:#2d7a44;"></td>
                            <td style="height:5px;width:33%;background-color:#0d3d26;"></td>
                        </tr>
                    </table>
                </div>

                <p class="cert-label">Diberikan kepada:</p>
                <p class="cert-recipient">{{ $namaSekolahCert }}</p>

                <div class="cert-npsn-wrap">
                    <div class="cert-npsn-label">Nomor Pokok Sekolah Nasional (NPSN)</div>
                    <div class="cert-npsn-val">{{ $reg->npsn }}</div>
                </div>

                <p class="cert-body">
                    Sebagai satuan pendidikan yang tercatat melakukan pengajuan pendaftaran di bawah naungan
                    <strong>{{ $kopLpReadable }}</strong>, setelah melengkapi data dan pengajuan Nota Kesepahaman (MoU)
                    melalui sistem sesuai ketentuan yang berlaku.
                </p>

                <p class="cert-issued">Diterbitkan di {{ $kotaSurat }}, pada {{ $tanggalSurat }}</p>

                <div class="ttd-wrap">
                    <div class="ttd-inner">
                        @if ($hasStampUrl && $hasSigUrl)
                            {{-- Tumpuk stempel + TTD: margin negatif (DomPDF sering gagal position:absolute di dalam sel) --}}
                            <div style="display:inline-block;width:215px;height:128px;margin:10px auto;text-align:left;">
                                <img src="{{ $stampDataUrl }}" alt="" width="120" height="120" style="display:block;width:120px;height:120px;margin-left:4px;">
                                <img src="{{ $ketuaTtdDataUrl }}" alt="" width="120" height="120" style="display:block;width:120px;height:120px;margin:-112px 0 0 62px;">
                            </div>
                        @elseif ($hasStampUrl)
                            <div class="ttd-combo-single">
                                <img src="{{ $stampDataUrl }}" alt="" width="120" height="120" style="max-width:120px;max-height:120px;">
                            </div>
                        @elseif ($hasSigUrl)
                            <div class="ttd-combo-single">
                                <img src="{{ $ketuaTtdDataUrl }}" alt="" width="120" height="120" style="max-width:120px;max-height:120px;">
                            </div>
                        @else
                            <div class="ttd-combo-empty">
                                <span class="placeholder-stamp">Stempel LP<br>(pengaturan cabang)</span>
                                <div class="sig-placeholder"></div>
                                <div style="font-size:7px;color:#64748b;margin-top:4px;">Unggah stempel &amp; tanda tangan PNG transparan di pengaturan cabang.</div>
                            </div>
                        @endif

                        <div class="cert-sign-name">{{ $namaKetua }}</div>
                        <div class="cert-sign-title">{!! nl2br(e($jabatanKetua)) !!}</div>
                    </div>
                </div>
            </div>

            <p class="foot">
                Dokumen ini dihasilkan otomatis oleh sistem Numa-Edu &middot; {{ config('app.url') }}
            </p>
        </div>
    </div>
</body>
</html>
