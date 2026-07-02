<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; line-height: 1.45; }
        h1 { font-size: 18px; margin: 0 0 4px; color: #0d4a2c; text-transform: uppercase; letter-spacing: 0.5px; }
        .sub { font-size: 11px; color: #555; margin-bottom: 18px; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.meta td { padding: 5px 8px; vertical-align: top; border: 1px solid #e5e7eb; }
        table.meta td.label { width: 28%; background: #f9fafb; font-weight: bold; color: #374151; }
        .amount-box { border: 2px solid #0d4a2c; padding: 14px 16px; margin: 18px 0; text-align: center; }
        .amount-label { font-size: 10px; text-transform: uppercase; color: #555; letter-spacing: 0.5px; }
        .amount-value { font-size: 22px; font-weight: bold; color: #0d4a2c; font-family: DejaVu Sans Mono, monospace; margin-top: 4px; }
        .amount-words { font-size: 11px; color: #444; margin-top: 6px; font-style: italic; }
        .sig { margin-top: 36px; width: 100%; }
        .sig td { width: 50%; text-align: center; vertical-align: top; padding-top: 8px; }
        .sig-space { height: 56px; }
        .footer { margin-top: 24px; font-size: 10px; color: #666; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    @include('pdf.partials.keuangan-kop', ['sekolah' => $sekolah, 'tanggalCetak' => $tanggalCetak])

    <h1>{{ __('Kwitansi Pembayaran') }}</h1>
    <div class="sub">{{ __('No.') }} {{ $nomorKwitansi }}</div>

    <table class="meta">
        <tr>
            <td class="label">{{ __('Sudah terima dari') }}</td>
            <td colspan="3">{{ $tagihan->siswa?->nama ?? '—' }} ({{ __('NIS') }}: {{ $tagihan->siswa?->nis ?? '—' }})</td>
        </tr>
        <tr>
            <td class="label">{{ __('Uraian') }}</td>
            <td colspan="3">{{ __('Pembayaran') }} {{ $tagihan->jenis }} — {{ __('Periode') }} {{ $tagihan->periode }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Tanggal bayar') }}</td>
            <td>{{ \App\Support\DateTimeFormat::datetime($pembayaran->dibayar_pada) }}</td>
            <td class="label">{{ __('Metode') }}</td>
            <td>
                {{ match ($pembayaran->metode) {
                    'tunai' => __('Tunai'),
                    'transfer' => __('Transfer bank'),
                    'virtual' => __('Virtual account'),
                    'lainnya' => __('Lainnya'),
                    default => $pembayaran->metode,
                } }}
            </td>
        </tr>
        @if ($pembayaran->referensi)
            <tr>
                <td class="label">{{ __('Referensi / no. bukti') }}</td>
                <td colspan="3">{{ $pembayaran->referensi }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">{{ __('Kelas') }}</td>
            <td>
                @if ($tagihan->siswa?->kelas)
                    {{ $tagihan->siswa->kelas->tingkat }} {{ $tagihan->siswa->kelas->nama }}
                @else
                    —
                @endif
            </td>
            <td class="label">{{ __('No. invoice') }}</td>
            <td>{{ app(\App\Services\KeuanganPdfService::class)->nomorInvoice($tagihan) }}</td>
        </tr>
    </table>

    <div class="amount-box">
        <div class="amount-label">{{ __('Jumlah diterima') }}</div>
        <div class="amount-value">Rp {{ number_format((float) $pembayaran->jumlah, 0, ',', '.') }}</div>
    </div>

    <table class="sig">
        <tr>
            <td>
                <div>{{ __('Penerima') }},</div>
                <div class="sig-space"></div>
                <div style="font-weight:bold;">(___________________________)</div>
            </td>
            <td>
                <div>{{ __('Penyetor / Wali') }},</div>
                <div class="sig-space"></div>
                <div style="font-weight:bold;">(___________________________)</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        {{ __('Dicatat oleh') }}: {{ $pembayaran->dicatatOleh?->name ?? '—' }}
        · {{ __('Dokumen ini dicetak otomatis dari') }} {{ config('app.name') }}.
    </div>
</body>
</html>
