<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Pencarian') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Hasil pencarian cepat dari modul utama.') }}</p>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
            <form method="GET" action="{{ route('search.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kata kunci') }}</label>
                    <input
                        type="search"
                        name="q"
                        value="{{ $q }}"
                        placeholder="{{ __('Contoh: Budi / 12345 / X IPA / 2025-2026') }}"
                        class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
                    />
                </div>
                <button type="submit" class="mt-6 inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Cari') }}
                </button>
            </form>
            @if (! $qMin && $q !== '')
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {{ __('Masukkan minimal 2 karakter.') }}
                </div>
            @endif
        </div>

        @if ($qMin)
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ __('Siswa') }}</div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($siswas as $s)
                            <a href="{{ route('siswa.edit', $s) }}" class="block px-5 py-3 hover:bg-gray-50/80">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $s->nama }}</div>
                                        <div class="mt-0.5 text-xs text-gray-500 font-mono">{{ $s->nis }}@if ($s->nisn) · {{ $s->nisn }}@endif</div>
                                    </div>
                                    <div class="text-right text-xs text-gray-500">
                                        {{ $s->kelas?->tingkat }} {{ $s->kelas?->nama }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-gray-500">{{ __('Tidak ada hasil.') }}</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ __('Kelas') }}</div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($kelas as $k)
                            <a href="{{ route('kelas.edit', $k) }}" class="block px-5 py-3 hover:bg-gray-50/80">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="font-semibold text-gray-900">{{ $k->tingkat }} {{ $k->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $k->tahun_ajaran }}</div>
                                </div>
                            </a>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-gray-500">{{ __('Tidak ada hasil.') }}</div>
                        @endforelse
                    </div>
                </div>

                @if (auth()->user()->hasAnyRole(['super_admin', 'admin']))
                    <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
                        <div class="border-b border-gray-100 px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ __('Tagihan') }}</div>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse ($tagihans as $t)
                                <a href="{{ route('tagihan.edit', $t) }}" class="block px-5 py-3 hover:bg-gray-50/80">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $t->siswa?->nama }}</div>
                                            <div class="mt-0.5 text-xs text-gray-500">{{ $t->jenis }} · {{ $t->periode }}</div>
                                        </div>
                                        <div class="text-right text-xs font-semibold {{ $t->status === 'unpaid' ? 'text-red-700' : 'text-emerald-700' }}">
                                            {{ strtoupper($t->status) }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-5 py-8 text-center text-sm text-gray-500">{{ __('Tidak ada hasil.') }}</div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>

