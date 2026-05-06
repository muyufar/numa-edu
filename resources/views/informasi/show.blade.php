@extends('layouts.informasi')

@section('title', $berita->judul.' — '.config('app.name'))

@section('content')
    <div class="mx-auto max-w-3xl">
        <nav class="mb-6 text-sm" aria-label="{{ __('Breadcrumb') }}">
            <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-gray-500">
                <li>
                    <a href="{{ url('/') }}" class="font-medium text-nu-primary hover:underline">{{ __('Beranda') }}</a>
                </li>
                <li aria-hidden="true" class="text-gray-300">/</li>
                <li>
                    <a href="{{ route('informasi.index') }}" class="font-medium text-nu-primary hover:underline">{{ __('Informasi') }}</a>
                </li>
                <li aria-hidden="true" class="text-gray-300">/</li>
                <li class="max-w-[min(100%,14rem)] truncate font-medium text-gray-700" title="{{ $berita->judul }}">{{ $berita->judul }}</li>
            </ol>
        </nav>

        <article class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="h-1.5 bg-gradient-to-r from-nu-primary via-nu-primary-light to-nu-gold"></div>
            <div class="px-6 py-8 sm:px-10 sm:py-10">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-gray-700 ring-1 ring-gray-200/80">
                        <svg class="h-3.5 w-3.5 text-nu-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \App\Support\DateTimeFormat::datetime($berita->published_at) }}
                    </span>
                </div>

                <h1 class="mt-4 text-2xl font-bold leading-tight tracking-tight text-gray-900 sm:text-3xl">{{ $berita->judul }}</h1>

                @if ($berita->ringkasan)
                    <p class="mt-5 border-l-4 border-nu-gold/80 pl-4 text-base leading-relaxed text-gray-700 sm:text-lg">{{ $berita->ringkasan }}</p>
                @endif

                <div class="mt-8 text-base leading-[1.75] text-gray-800 sm:text-lg">
                    {!! nl2br(e($berita->isi)) !!}
                </div>
            </div>
        </article>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('informasi.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-black/5 transition hover:border-nu-primary/25 hover:bg-gray-50">
                <svg class="h-4 w-4 text-nu-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Kembali ke daftar') }}
            </a>
            <a href="{{ url('/') }}" class="text-center text-sm font-semibold text-nu-primary hover:underline sm:text-right">{{ __('Ke beranda') }}</a>
        </div>
    </div>
@endsection
