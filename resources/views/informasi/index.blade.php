@extends('layouts.informasi')

@section('title', __('Informasi').' — '.config('app.name'))

@section('content')
    <div class="mx-auto max-w-4xl">
        <header class="relative overflow-hidden rounded-2xl border border-nu-primary/10 bg-gradient-to-br from-white via-white to-nu-cream/60 px-6 py-8 shadow-sm ring-1 ring-black/5 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-nu-primary/5 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-12 -left-12 h-32 w-32 rounded-full bg-nu-gold/10 blur-2xl"></div>
            <p class="relative text-xs font-bold uppercase tracking-wider text-nu-primary">{{ __('Portal resmi') }}</p>
            <h1 class="relative mt-2 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">{{ __('Informasi & berita') }}</h1>
            <p class="relative mt-3 max-w-2xl text-sm leading-relaxed text-gray-600 sm:text-base">{{ __('Pengumuman resmi sekolah. Baca ringkasan di bawah, lalu buka halaman lengkap untuk detail.') }}</p>
        </header>

        <div class="mt-10 grid gap-5 sm:grid-cols-1 lg:grid-cols-2 lg:gap-6">
            @forelse ($beritas as $b)
                @php
                    $d = $b->published_at;
                @endphp
                <article class="group relative overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm ring-1 ring-black/5 transition duration-200 hover:border-nu-primary/20 hover:shadow-md">
                    <a href="{{ route('informasi.show', $b->slug) }}" class="flex gap-4 p-5 sm:gap-5 sm:p-6">
                        <div class="flex w-16 shrink-0 flex-col items-center justify-center rounded-xl bg-gradient-to-b from-nu-primary/10 to-nu-primary/5 px-2 py-3 text-center ring-1 ring-nu-primary/10 sm:w-[4.5rem]">
                            <span class="font-mono text-sm font-extrabold leading-none text-nu-primary">{{ \App\Support\DateTimeFormat::date($d, '') }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-base font-bold leading-snug tracking-tight text-gray-900 transition group-hover:text-nu-primary sm:text-lg">
                                {{ $b->judul }}
                            </h2>
                            @if ($b->ringkasan)
                                <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-gray-600">{{ $b->ringkasan }}</p>
                            @endif
                            <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-nu-primary">
                                {{ __('Baca selengkapnya') }}
                                <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </a>
                </article>
            @empty
                <div class="lg:col-span-2">
                    <div class="nu-surface flex flex-col items-center px-6 py-14 text-center ring-1 ring-black/5">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-nu-primary/10 text-nu-primary ring-1 ring-nu-primary/15">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                        <p class="mt-4 text-sm font-semibold text-gray-900">{{ __('Belum ada informasi') }}</p>
                        <p class="mt-1 max-w-sm text-sm leading-relaxed text-gray-600">{{ __('Pengumuman akan muncul di sini setelah diterbitkan oleh admin.') }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($beritas->hasPages())
            <div class="mt-10 flex justify-center">
                <div class="nu-surface inline-flex rounded-xl px-2 py-1 ring-1 ring-black/5">
                    {{ $beritas->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
