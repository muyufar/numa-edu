<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-nu-cream to-gray-100 font-sans text-gray-900 antialiased">
    <header class="sticky top-0 z-30 border-b border-gray-200/80 bg-white/80 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl flex-wrap items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-nu-primary">
                <x-application-logo class="h-8 w-8 shrink-0 text-nu-gold" />
                {{ config('app.name') }}
            </a>
            <nav class="flex flex-wrap items-center gap-4 text-sm font-semibold">
                <a href="{{ route('informasi.index') }}" class="text-gray-700 hover:text-nu-primary {{ request()->routeIs('informasi.*') ? 'text-nu-primary' : '' }}">{{ __('Informasi') }}</a>
                <a href="{{ route('ppdb.daftar') }}" class="text-gray-700 hover:text-nu-primary">{{ __('PPDB') }}</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-nu-primary hover:underline">{{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="text-nu-primary hover:underline">{{ __('Log in') }}</a>
                @endauth
            </nav>
        </div>
    </header>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>
</body>
</html>
