<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Kartu presensi') }} — {{ $record->nama }}</title>
    @vite(['resources/css/app.css', 'resources/js/presensi-kartu.js'])
</head>
<body class="min-h-screen bg-gray-100 font-sans text-gray-900 antialiased">
    <div id="presensi-kartu-root" data-kode="{{ $record->presensi_kode }}" class="mx-auto max-w-md p-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg ring-1 ring-black/5">
            <div class="text-center">
                <div class="text-xs font-bold uppercase tracking-widest text-nu-primary">{{ config('app.name') }}</div>
                <h1 class="mt-2 text-lg font-bold text-gray-900">{{ $record->nama }}</h1>
                @if ($subtitle)
                    <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="mt-6 flex justify-center">
                <canvas id="presensi-kartu-qr" width="220" height="220" class="rounded-xl bg-white ring-1 ring-gray-200"></canvas>
            </div>

            <p id="presensi-kartu-qr-fallback" class="mt-4 hidden rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-center text-xs text-amber-900">
                {{ __('QR tidak dapat ditampilkan. Muat ulang halaman atau gunakan kode di bawah untuk scan manual.') }}
            </p>

            <p class="mt-4 text-center font-mono text-sm font-semibold text-gray-800">{{ $record->presensi_kode }}</p>
            <p class="mt-1 text-center text-xs text-gray-500">{{ __('Scan kode ini di halaman presensi sekolah.') }}</p>

            @if ($record->face_descriptor)
                <p class="mt-4 text-center text-xs font-semibold text-emerald-700">{{ __('Wajah terdaftar') }}</p>
            @else
                <p class="mt-4 text-center text-xs text-amber-700">{{ __('Wajah belum didaftarkan') }}</p>
            @endif
        </div>

        <div class="mt-4 flex flex-wrap justify-center gap-2 print:hidden">
            <button type="button" onclick="window.print()" class="inline-flex items-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                {{ __('Cetak') }}
            </button>
            <a href="{{ $scanRoute }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Buka scanner') }}
            </a>
        </div>
    </div>
</body>
</html>
