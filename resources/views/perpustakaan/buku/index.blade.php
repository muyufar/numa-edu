<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ ($isSiswaDigitalView ?? false) ? __('Perpustakaan digital') : __('Katalog Perpustakaan') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    @if ($isSiswaDigitalView ?? false)
                        {{ __('Pinjam dan baca e-book sekolah.') }}
                    @else
                        {{ __('Cari dan pinjam buku fisik maupun digital.') }}
                    @endif
                </p>
            </div>
            @can('create', \App\Models\PerpustakaanBuku::class)
                <a href="{{ route('perpustakaan.buku.create') }}" class="btn-nu-primary">{{ __('Tambah buku') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        <form method="GET" class="grid gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-black/5 sm:grid-cols-4">
            @if ($digitalOnly ?? false)
                <input type="hidden" name="digital" value="1" />
            @endif
            <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('Cari judul, pengarang, ISBN') }}" class="rounded-xl border-gray-200 text-sm sm:col-span-2">
            @unless ($isSiswaDigitalView ?? false)
            <select name="tipe" class="rounded-xl border-gray-200 text-sm">
                <option value="">{{ __('Semua tipe') }}</option>
                @foreach (\App\Models\PerpustakaanBuku::TIPE_OPTIONS as $t)
                    <option value="{{ $t }}" @selected($tipe === $t)>{{ (new \App\Models\PerpustakaanBuku(['tipe' => $t]))->labelTipe() }}</option>
                @endforeach
            </select>
            @else
            <div class="flex items-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-900">
                {{ __('E-book') }}
            </div>
            @endunless
            <button class="btn-nu-primary justify-center">{{ __('Filter') }}</button>
        </form>

        <div class="flex flex-wrap gap-4">
            @forelse ($bukus as $buku)
                <a href="{{ route('perpustakaan.buku.show', $buku) }}" class="group w-32 shrink-0 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition hover:ring-nu-primary/30 sm:w-36">
                    <div class="h-40 overflow-hidden bg-gray-100 sm:h-44">
                        @if ($buku->coverUrl())
                            <img src="{{ $buku->coverUrl() }}" alt="{{ $buku->judul }}" class="h-full w-full object-cover object-center">
                        @else
                            <div class="flex h-full items-center justify-center px-2 text-center text-[10px] leading-tight text-gray-400">{{ __('Tanpa cover') }}</div>
                        @endif
                    </div>
                    <div class="p-2.5">
                        <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 {{ $buku->badgeTipeClass() }}">{{ $buku->labelTipe() }}</span>
                        <h3 class="mt-1.5 line-clamp-2 text-sm font-bold text-gray-900 group-hover:text-nu-primary">{{ $buku->judul }}</h3>
                        <p class="mt-1 line-clamp-1 text-xs text-gray-600">{{ $buku->pengarang ?: '—' }}</p>
                        @if ($buku->supportsFisik())
                            <p class="mt-2 text-xs text-gray-500">{{ __('Tersedia:') }} {{ $buku->eksemplar_tersedia }}/{{ $buku->jumlah_eksemplar }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <div class="w-full rounded-2xl bg-white p-8 text-center text-gray-500 ring-1 ring-black/5">{{ __('Belum ada buku.') }}</div>
            @endforelse
        </div>

        {{ $bukus->links() }}
    </div>
</x-app-layout>
