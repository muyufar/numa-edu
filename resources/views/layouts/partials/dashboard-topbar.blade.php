<header
    class="fixed top-0 right-0 z-40 border-b border-gray-200/80 bg-white left-0"
    :class="sidebarCollapsed ? 'lg:left-24' : 'lg:left-72'"
>
    <div class="flex h-16 items-center gap-3 px-4 pt-4 lg:px-6">
        <button
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 shadow-sm hover:bg-gray-50 lg:hidden"
            @click="sidebarOpen = true"
            aria-label="{{ __('Buka menu') }}"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <div class="hidden min-w-0 flex-1 items-center md:flex">
            <div class="relative w-full max-w-2xl">
                <form method="GET" action="{{ route('search.index') }}">
                    <div class="flex h-11 w-full items-center gap-2 rounded-xl border border-gray-200 bg-white px-2 shadow-sm outline-none focus-within:border-nu-primary focus-within:outline-none focus-within:ring-0">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                        </span>
                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            class="nu-topbar-search-input h-10 min-w-0 flex-1 border-0 bg-transparent text-sm text-gray-900 placeholder:text-gray-400 outline-none focus:outline-none focus:ring-0 focus-visible:outline-none appearance-none"
                            placeholder="{{ __('Cari siswa, kelas, tagihan…') }}"
                        />
                        <button type="submit" class="inline-flex h-9 items-center rounded-lg bg-gray-900 px-3 text-[11px] font-semibold text-white hover:bg-gray-800 focus:outline-none focus-visible:outline-none focus-visible:ring-0">
                            {{ __('Cari') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="ms-auto flex items-center gap-2">
            <span class="hidden text-sm text-gray-600 lg:inline">
                <span class="font-semibold text-gray-900">{{ __('Halo') }},</span>
                {{ Auth::user()->name }}
            </span>

            <x-dropdown align="right" width="w-80" contentClasses="py-0 bg-white">
                <x-slot name="trigger">
                    @include('notifications._bell')
                </x-slot>
                <x-slot name="content">
                    @include('notifications._dropdown')
                </x-slot>
            </x-dropdown>

            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-50">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-nu-primary text-xs font-bold text-white">
                            {{ strtoupper(substr((string) Auth::user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden sm:inline max-w-[10rem] truncate">{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-3 border-b border-gray-100 sm:hidden">
                        <div class="text-xs text-gray-500">{{ __('Masuk sebagai') }}</div>
                        <div class="mt-0.5 truncate text-sm font-semibold text-gray-900">{{ Auth::user()->email }}</div>
                    </div>
                    <x-dropdown-link :href="route('profile.edit')">{{ __('Profil') }}</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Keluar') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>
