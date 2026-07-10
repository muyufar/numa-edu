@php
    $tabLink = static fn (string $name): string => route('tenaga-kependidikan.index', ['tab' => $name]);
    $tabClass = static fn (bool $active): string => $active
        ? 'border-nu-primary text-nu-primary'
        : 'border-transparent text-gray-500 hover:border-gray-200 hover:text-gray-700';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Guru dan Tendik') }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    @if ($tab === 'guru')
                        {{ __('Kelola data guru dan akun masuk.') }}
                    @else
                        {{ __('Kelola tenaga kependidikan non-guru untuk presensi dan administrasi.') }}
                    @endif
                </p>
            </div>

            @if ($tab === 'guru')
                @can('create', \App\Models\Guru::class)
                    <div class="flex items-center gap-3">
                        <a href="{{ route('tenaga-kependidikan.index', ['tab' => 'guru', 'import' => 1]) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                            {{ __('Import GTK') }}
                        </a>
                        <a href="{{ route('guru.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                            <span class="me-2 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </span>
                            {{ __('Tambah guru') }}
                        </a>
                    </div>
                @endcan
            @else
                @can('create', \App\Models\Pegawai::class)
                    <div class="flex items-center gap-3">
                        <a href="{{ route('tenaga-kependidikan.index', ['tab' => 'pegawai', 'import' => 1]) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                            {{ __('Import GTK') }}
                        </a>
                        <a href="{{ route('pegawai.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                            {{ __('Tambah tenaga kependidikan') }}
                        </a>
                    </div>
                @endcan
            @endif
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                {{ session('error') }}
            </div>
        @endif

        @if ($canViewGuru && $canViewPegawai)
            <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
                <nav class="flex gap-1 px-5" aria-label="{{ __('Tab guru dan tendik') }}">
                    <a
                        href="{{ $tabLink('guru') }}"
                        class="inline-flex items-center border-b-2 px-1 py-4 text-sm font-semibold transition {{ $tabClass($tab === 'guru') }}"
                    >
                        {{ __('Guru') }}
                    </a>
                    <a
                        href="{{ $tabLink('pegawai') }}"
                        class="inline-flex items-center border-b-2 px-1 py-4 text-sm font-semibold transition {{ $tabClass($tab === 'pegawai') }}"
                    >
                        {{ __('Tenaga Kependidikan') }}
                    </a>
                </nav>
            </div>
        @endif

        @if ($tab === 'guru')
            @include('tenaga-kependidikan._tab-guru', ['gurus' => $gurus])
        @else
            @include('tenaga-kependidikan._tab-pegawai', ['pegawais' => $pegawais])
        @endif
    </div>
</x-app-layout>
