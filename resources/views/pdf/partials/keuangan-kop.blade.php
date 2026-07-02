@php
    $namaSekolah = $sekolah?->nama ?? config('app.name');
    $alamat = collect([
        $sekolah?->alamat,
        $sekolah?->alamatWilayahRingkas(),
    ])->filter()->implode(' · ');
    $kontak = collect([
        $sekolah?->telepon ? __('Telp') . ' ' . $sekolah->telepon : null,
        $sekolah?->email_kantor,
    ])->filter()->implode(' · ');
@endphp
<table style="width:100%;border-bottom:2px solid #0d4a2c;margin-bottom:16px;padding-bottom:10px;">
    <tr>
        <td style="vertical-align:top;">
            <div style="font-size:16px;font-weight:bold;color:#0d4a2c;text-transform:uppercase;">{{ $namaSekolah }}</div>
            @if ($alamat !== '')
                <div style="font-size:10px;color:#444;margin-top:4px;line-height:1.4;">{{ $alamat }}</div>
            @endif
            @if ($kontak !== '')
                <div style="font-size:10px;color:#666;margin-top:2px;">{{ $kontak }}</div>
            @endif
        </td>
        <td style="vertical-align:top;text-align:right;width:38%;">
            <div style="font-size:10px;color:#666;">{{ __('Dicetak') }}</div>
            <div style="font-size:11px;font-weight:bold;color:#111;">{{ $tanggalCetak ?? now()->locale('id')->translatedFormat('d F Y') }}</div>
        </td>
    </tr>
</table>
