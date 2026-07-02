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
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.items th { background: #0d4a2c; color: #fff; font-size: 10px; text-transform: uppercase; padding: 8px; text-align: left; }
        table.items td { padding: 8px; border: 1px solid #e5e7eb; }
        table.items td.num { text-align: right; font-family: DejaVu Sans Mono, monospace; }
        .totals { width: 100%; margin-top: 14px; }
        .totals td { padding: 4px 0; }
        .totals .label { text-align: right; padding-right: 12px; color: #555; }
        .totals .value { text-align: right; font-family: DejaVu Sans Mono, monospace; font-weight: bold; width: 140px; }
        .status { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 10px; font-weight: bold; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-partial { background: #fef3c7; color: #92400e; }
        .status-unpaid { background: #f3f4f6; color: #374151; }
        .footer { margin-top: 24px; font-size: 10px; color: #666; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    @include('pdf.partials.keuangan-kop', ['sekolah' => $sekolah, 'tanggalCetak' => $tanggalCetak])

    <h1>{{ __('Invoice Tagihan') }}</h1>
    <div class="sub">{{ __('No.') }} {{ $nomorInvoice }}</div>

    <table class="meta">
        <tr>
            <td class="label">{{ __('Siswa') }}</td>
            <td>{{ $tagihan->siswa?->nama ?? '—' }}</td>
            <td class="label">{{ __('NIS') }}</td>
            <td>{{ $tagihan->siswa?->nis ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Kelas') }}</td>
            <td>
                @if ($tagihan->siswa?->kelas)
                    {{ $tagihan->siswa->kelas->tingkat }} {{ $tagihan->siswa->kelas->nama }}
                @else
                    —
                @endif
            </td>
            <td class="label">{{ __('Periode') }}</td>
            <td>{{ $tagihan->periode }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Jenis tagihan') }}</td>
            <td>{{ $tagihan->jenis }}</td>
            <td class="label">{{ __('Status') }}</td>
            <td>
                @php
                    $statusClass = match ($tagihan->status) {
                        'paid' => 'status-paid',
                        'partial' => 'status-partial',
                        default => 'status-unpaid',
                    };
                    $statusLabel = match ($tagihan->status) {
                        'paid' => __('Lunas'),
                        'partial' => __('Sebagian'),
                        default => __('Belum lunas'),
                    };
                @endphp
                <span class="status {{ $statusClass }}">{{ $statusLabel }}</span>
            </td>
        </tr>
        @if ($tagihan->jatuh_tempo)
            <tr>
                <td class="label">{{ __('Jatuh tempo') }}</td>
                <td colspan="3">{{ \App\Support\DateTimeFormat::date($tagihan->jatuh_tempo) }}</td>
            </tr>
        @endif
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>{{ __('Uraian') }}</th>
                <th style="width:120px;text-align:right;">{{ __('Nominal') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $tagihan->jenis }} — {{ __('Periode') }} {{ $tagihan->periode }}</td>
                <td class="num">Rp {{ number_format((float) $tagihan->jumlah, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">{{ __('Total tagihan') }}</td>
            <td class="value">Rp {{ number_format((float) $tagihan->jumlah, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Sudah dibayar') }}</td>
            <td class="value">Rp {{ number_format($tagihan->totalDibayar(), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label" style="font-size:13px;color:#111;">{{ __('Sisa') }}</td>
            <td class="value" style="font-size:13px;color:#0d4a2c;">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if ($tagihan->pembayarans->isNotEmpty())
        <div style="margin-top:20px;font-weight:bold;font-size:11px;text-transform:uppercase;color:#374151;">{{ __('Riwayat pembayaran') }}</div>
        <table class="items" style="margin-top:6px;">
            <thead>
                <tr>
                    <th>{{ __('Tanggal') }}</th>
                    <th style="width:110px;text-align:right;">{{ __('Jumlah') }}</th>
                    <th style="width:80px;">{{ __('Metode') }}</th>
                    <th>{{ __('Referensi') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tagihan->pembayarans as $p)
                    <tr>
                        <td>{{ \App\Support\DateTimeFormat::datetime($p->dibayar_pada) }}</td>
                        <td class="num">Rp {{ number_format((float) $p->jumlah, 0, ',', '.') }}</td>
                        <td>{{ $p->metode }}</td>
                        <td>{{ $p->referensi ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        {{ __('Dokumen ini dicetak otomatis dari') }} {{ config('app.name') }}. {{ __('Invoice bukan bukti pembayaran resmi; gunakan kwitansi untuk setiap transaksi pembayaran.') }}
    </div>
</body>
</html>
