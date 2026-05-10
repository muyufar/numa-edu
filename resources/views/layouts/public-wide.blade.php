<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-b from-nu-cream to-gray-100 text-gray-900">
        <div class="min-h-screen">
            <header class="sticky top-0 z-30 border-b border-gray-200/80 bg-white/80 backdrop-blur">
                <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-nu-primary">
                        <x-application-logo class="h-8 w-8 shrink-0 text-nu-gold" />
                        {{ config('app.name') }}
                    </a>

                    <nav class="flex flex-wrap items-center justify-end gap-3 text-sm font-semibold">
                        <a href="{{ route('informasi.index') }}" class="text-gray-700 hover:text-nu-primary">{{ __('Informasi') }}</a>
                        <a href="{{ route('ppdb.daftar') }}" class="text-gray-700 hover:text-nu-primary">{{ __('PPDB') }}</a>
                        <a href="{{ route('public.lembaga-registrations.create') }}" class="text-gray-700 hover:text-nu-primary">{{ __('Pendaftaran lembaga') }}</a>
                        <a href="{{ route('public.lembaga-registrations.check-status') }}" class="text-gray-700 hover:text-nu-primary">{{ __('Cek status (NPSN)') }}</a>
                        <a href="{{ route('login') }}" class="text-nu-primary hover:underline">{{ __('Log in') }}</a>
                    </nav>
                </div>
            </header>

            <main class="mx-auto w-full max-w-3xl px-4 py-6 sm:px-6 sm:py-8 md:px-10 md:py-10 lg:px-12 lg:py-12">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
