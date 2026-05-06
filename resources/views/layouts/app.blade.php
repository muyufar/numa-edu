<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="margin:0;padding:0">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-b from-nu-cream to-gray-100 text-gray-900" style="margin:0;padding:0">
        <div
            x-data="{
                sidebarOpen: false,
                sidebarCollapsed: false,
                init() {
                    try {
                        this.sidebarCollapsed = localStorage.getItem('nu_sidebar_collapsed') === '1';
                    } catch (e) {}
                },
                toggleSidebarCollapsed() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    try {
                        localStorage.setItem('nu_sidebar_collapsed', this.sidebarCollapsed ? '1' : '0');
                    } catch (e) {}
                },
            }"
            class="min-h-screen"
        >
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                x-cloak
                class="fixed inset-0 z-40 bg-black/45 lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 transform transition-all duration-200 ease-out -translate-x-full lg:translate-x-0"
                :class="[
                    sidebarCollapsed ? 'w-24' : 'w-72',
                    sidebarOpen ? '!translate-x-0' : '',
                ]"
            >
                @include('layouts.partials.dashboard-sidebar')
            </aside>

            <div class="transition-all duration-300 ease-out" :class="sidebarCollapsed ? 'lg:pl-24' : 'lg:pl-72'">
                @include('layouts.partials.dashboard-topbar')
                <div class="h-16" aria-hidden="true"></div>

                @isset($header)
                    <div class="border-b border-gray-200/80 bg-white">
                        <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
