@php /** @var \App\Models\PerpustakaanBuku $buku */ @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $buku->badgeTipeClass() }}">{{ $buku->labelTipe() }}</span>
                <h2 class="mt-2 text-xl font-extrabold text-gray-900">{{ $buku->judul }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $buku->pengarang }} @if($buku->penerbit) · {{ $buku->penerbit }} @endif</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('perpustakaan.buku.index') }}" class="btn-nu">{{ __('Katalog') }}</a>
                @can('update', $buku)
                    <a href="{{ route('perpustakaan.buku.edit', $buku) }}" class="btn-nu">{{ __('Edit') }}</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif

        @if ($canPreview && $buku->isPdf())
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">
                <div class="border-b border-gray-100 px-5 py-4 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Baca e-book (PDF)') }}</h3>
                    <a href="{{ route('perpustakaan.buku.preview', $buku) }}" target="_blank" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Buka tab baru') }}</a>
                </div>
                <iframe src="{{ route('perpustakaan.buku.preview', $buku) }}" class="block w-full border-0 bg-gray-100" style="height: min(1200px, calc(100vh - 8rem)); min-height: 900px;" loading="lazy"></iframe>
            </div>
        @endif

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                        <div><dt class="text-xs uppercase text-gray-500">{{ __('Kategori') }}</dt><dd class="mt-1 font-semibold">{{ $buku->kategori?->nama ?: '—' }}</dd></div>
                        <div><dt class="text-xs uppercase text-gray-500">{{ __('ISBN') }}</dt><dd class="mt-1 font-semibold">{{ $buku->isbn ?: '—' }}</dd></div>
                        <div><dt class="text-xs uppercase text-gray-500">{{ __('Tahun') }}</dt><dd class="mt-1 font-semibold">{{ $buku->tahun_terbit ?: '—' }}</dd></div>
                        <div><dt class="text-xs uppercase text-gray-500">{{ __('Rak') }}</dt><dd class="mt-1 font-semibold">{{ $buku->rak_lokasi ?: '—' }}</dd></div>
                        @if ($buku->supportsFisik())
                            <div><dt class="text-xs uppercase text-gray-500">{{ __('Eksemplar tersedia') }}</dt><dd class="mt-1 font-semibold">{{ $buku->eksemplar_tersedia }} / {{ $buku->jumlah_eksemplar }}</dd></div>
                        @endif
                    </dl>
                    @if ($buku->sinopsis)
                        <div class="mt-6 border-t border-gray-100 pt-4">
                            <h4 class="text-xs font-bold uppercase text-gray-500">{{ __('Sinopsis') }}</h4>
                            <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $buku->sinopsis }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                @if ($coverUrl = $buku->coverUrl())
                    <img src="{{ $coverUrl }}" alt="{{ $buku->judul }}" class="w-full rounded-3xl shadow-sm ring-1 ring-black/5">
                @endif

                @if ($pinjamanAktif)
                    <div class="rounded-3xl bg-sky-50 p-5 ring-1 ring-sky-100">
                        <h3 class="text-sm font-bold text-sky-900">{{ __('Peminjaman aktif') }}</h3>
                        <p class="mt-2 text-sm text-sky-800">{{ __('Jatuh tempo:') }} {{ $pinjamanAktif->tanggal_jatuh_tempo->format('d M Y') }}</p>
                        <a href="{{ route('perpustakaan.peminjaman.show', $pinjamanAktif) }}" class="mt-3 inline-flex text-sm font-semibold text-nu-primary hover:underline">{{ __('Detail peminjaman') }}</a>
                    </div>
                @elseif ($canPinjam)
                    <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5 space-y-3">
                        <h3 class="text-sm font-bold text-gray-900">{{ __('Pinjam buku') }}</h3>
                        @if ($buku->supportsFisik() && $buku->eksemplar_tersedia > 0)
                            <form method="POST" action="{{ route('perpustakaan.buku.pinjam', $buku) }}">
                                @csrf
                                <input type="hidden" name="tipe_peminjaman" value="fisik">
                                <button class="btn-nu-primary w-full justify-center" type="submit">{{ __('Pinjam fisik') }}</button>
                            </form>
                        @endif
                        @if ($buku->supportsDigital())
                            <form method="POST" action="{{ route('perpustakaan.buku.pinjam', $buku) }}">
                                @csrf
                                <input type="hidden" name="tipe_peminjaman" value="digital">
                                <button class="btn-nu w-full justify-center" type="submit">{{ __('Pinjam digital') }}</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
