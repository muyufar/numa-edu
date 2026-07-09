<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">{{ __('Perpustakaan') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('Ringkasan koleksi dan peminjaman.') }}</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-2xl bg-white p-4 ring-1 ring-black/5"><div class="text-xs text-gray-500">{{ __('Total buku aktif') }}</div><div class="mt-1 text-2xl font-extrabold">{{ $stats['total_buku'] }}</div></div>
            <div class="rounded-2xl bg-amber-50 p-4 ring-1 ring-amber-100"><div class="text-xs text-amber-700">{{ __('Koleksi fisik') }}</div><div class="mt-1 text-2xl font-extrabold text-amber-900">{{ $stats['buku_fisik'] }}</div></div>
            <div class="rounded-2xl bg-sky-50 p-4 ring-1 ring-sky-100"><div class="text-xs text-sky-700">{{ __('E-book') }}</div><div class="mt-1 text-2xl font-extrabold text-sky-900">{{ $stats['buku_digital'] }}</div></div>
            <div class="rounded-2xl bg-violet-50 p-4 ring-1 ring-violet-100"><div class="text-xs text-violet-700">{{ __('Sedang dipinjam') }}</div><div class="mt-1 text-2xl font-extrabold text-violet-900">{{ $stats['dipinjam'] }}</div></div>
            <div class="rounded-2xl bg-red-50 p-4 ring-1 ring-red-100"><div class="text-xs text-red-700">{{ __('Terlambat') }}</div><div class="mt-1 text-2xl font-extrabold text-red-900">{{ $stats['terlambat'] }}</div></div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('perpustakaan.buku.index') }}" class="btn-nu-primary">{{ __('Katalog buku') }}</a>
            <a href="{{ route('perpustakaan.peminjaman.index') }}" class="btn-nu">{{ $isPetugas ? __('Semua peminjaman') : __('Peminjaman saya') }}</a>
            @can('viewAny', \App\Models\PerpustakaanKategori::class)
                <a href="{{ route('perpustakaan.kategori.index') }}" class="btn-nu">{{ __('Kategori') }}</a>
                <a href="{{ route('perpustakaan.pengaturan.edit') }}" class="btn-nu">{{ __('Pengaturan') }}</a>
            @endcan
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Peminjaman terbaru') }}</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($peminjamanTerbaru as $p)
                        <a href="{{ route('perpustakaan.peminjaman.show', $p) }}" class="block rounded-xl border border-gray-100 p-3 hover:bg-gray-50">
                            <div class="font-semibold text-gray-900">{{ $p->buku?->judul }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $p->namaPeminjam() }} · {{ $p->labelStatus() }}</div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('Belum ada peminjaman.') }}</p>
                    @endforelse
                </div>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Buku populer (3 bulan)') }}</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($bukuPopuler as $b)
                        <a href="{{ route('perpustakaan.buku.show', $b) }}" class="flex gap-3 rounded-xl border border-gray-100 p-3 hover:bg-gray-50">
                            <div class="h-16 w-12 shrink-0 overflow-hidden rounded-lg bg-gray-100 ring-1 ring-black/5">
                                @if ($coverUrl = $b->coverUrl())
                                    <img src="{{ $coverUrl }}" alt="{{ $b->judul }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center px-1 text-center text-[9px] leading-tight text-gray-400">{{ __('Tanpa cover') }}</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-gray-900">{{ $b->judul }}</div>
                                <div class="mt-1 text-xs text-gray-500">{{ $b->peminjamans_count }} {{ __('peminjaman') }}</div>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('Belum ada data.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
