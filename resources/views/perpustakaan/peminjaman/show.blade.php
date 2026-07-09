@php /** @var \App\Models\PerpustakaanPeminjaman $peminjaman */ @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $peminjaman->buku?->judul }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $peminjaman->namaPeminjam() }}</p>
            </div>
            <a href="{{ route('perpustakaan.peminjaman.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-xs uppercase text-gray-500">{{ __('Status') }}</dt><dd class="mt-1"><span class="rounded-full px-2 py-0.5 text-xs font-semibold ring-1 {{ $peminjaman->badgeStatusClass() }}">{{ $peminjaman->labelStatus() }}</span></dd></div>
                <div><dt class="text-xs uppercase text-gray-500">{{ __('Tipe') }}</dt><dd class="mt-1 font-semibold">{{ ucfirst($peminjaman->tipe_peminjaman) }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">{{ __('Tanggal pinjam') }}</dt><dd class="mt-1 font-semibold">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">{{ __('Jatuh tempo') }}</dt><dd class="mt-1 font-semibold">{{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}</dd></div>
                @if ($peminjaman->tanggal_kembali)<div><dt class="text-xs uppercase text-gray-500">{{ __('Dikembalikan') }}</dt><dd class="mt-1 font-semibold">{{ $peminjaman->tanggal_kembali->format('d M Y') }}</dd></div>@endif
                @if ($peminjaman->denda)<div><dt class="text-xs uppercase text-gray-500">{{ __('Denda') }}</dt><dd class="mt-1 font-semibold">Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}</dd></div>@endif
            </dl>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('perpustakaan.buku.show', $peminjaman->buku) }}" class="btn-nu">{{ __('Lihat buku') }}</a>

                @can('perpanjang', $peminjaman)
                    <form method="POST" action="{{ route('perpustakaan.peminjaman.perpanjang', $peminjaman) }}">@csrf<button class="btn-nu" type="submit">{{ __('Perpanjang') }}</button></form>
                @endcan

                @can('kembalikan', $peminjaman)
                    <form method="POST" action="{{ route('perpustakaan.peminjaman.kembalikan', $peminjaman) }}" onsubmit="return confirm('{{ __('Kembalikan buku ini?') }}')">@csrf<button class="btn-nu-primary" type="submit">{{ __('Kembalikan') }}</button></form>
                    <form method="POST" action="{{ route('perpustakaan.peminjaman.hilang', $peminjaman) }}" onsubmit="return confirm('{{ __('Tandai sebagai hilang?') }}')">@csrf<button class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700" type="submit">{{ __('Tandai hilang') }}</button></form>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
