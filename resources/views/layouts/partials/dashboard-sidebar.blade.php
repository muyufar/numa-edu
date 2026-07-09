@php
    use App\Support\SidebarNavigation;

    $linkBase = 'nu-navlink group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-300 ease-out';
    $active = 'bg-white/10 text-white ring-1 ring-white/10';
    $idle = 'text-white/75 hover:bg-white/5 hover:text-white';
@endphp

<div
    class="flex h-full min-h-0 flex-col bg-gradient-to-b from-nu-primary to-[#0a3d24] text-white shadow-xl"
    data-nu-sidebar
    :class="sidebarCollapsed ? 'nu-sidebar--collapsed' : ''"
>
    <style>
        [data-nu-sidebar].nu-sidebar--collapsed nav .nu-navlink {
            justify-content: center;
            gap: 0.25rem;
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }
        [data-nu-sidebar].nu-sidebar--collapsed nav .nu-navlink.ml-6 {
            margin-left: 0 !important;
        }
        [data-nu-sidebar].nu-sidebar--collapsed nav .nu-navlink span {
            display: none;
        }
        [data-nu-sidebar].nu-sidebar--collapsed nav .nu-navlink svg {
            width: 1.45rem;
            height: 1.45rem;
            transition: width 220ms ease, height 220ms ease;
        }
        [data-nu-sidebar].nu-sidebar--collapsed nav .nu-navlink .nu-chevron {
            display: none;
        }
    </style>
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-4" :class="sidebarCollapsed ? 'px-3' : 'px-4'">
        <x-application-logo class="h-9 w-9 shrink-0 text-nu-gold" />
        <div class="min-w-0 leading-tight" x-show="!sidebarCollapsed" x-cloak>
            <div class="truncate text-[11px] font-semibold uppercase tracking-wider text-nu-gold">Ma'arif</div>
            <div class="truncate text-sm font-bold">{{ config('app.name') }}</div>
        </div>
        <div class="ms-auto flex items-center gap-1">
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-white/80 hover:bg-white/10"
                @click="toggleSidebarCollapsed()"
                :aria-label="sidebarCollapsed ? '{{ __('Tampilkan sidebar') }}' : '{{ __('Sembunyikan sidebar') }}'"
                title="{{ __('Hide/Unhide sidebar') }}"
            >
                <svg class="h-5 w-5" :class="sidebarCollapsed ? 'h-6 w-6' : 'h-5 w-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H21M10 12H21M10 18H21M3 6h.01M3 12h.01M3 18h.01" />
                </svg>
            </button>
        <button
            type="button"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-white/80 hover:bg-white/10 lg:hidden"
            @click="sidebarOpen = false"
            aria-label="{{ __('Tutup menu') }}"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        </div>
    </div>

    <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto px-3 py-4" :class="sidebarCollapsed ? 'px-2' : 'px-3'">
        <a href="{{ route('dashboard') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $active : $idle }}">
            <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span x-show="!sidebarCollapsed" x-cloak>{{ __('Ringkasan') }}</span>
        </a>

        @if (auth()->user()->hasRole('admin') && auth()->user()->sekolah_id)
            <a href="{{ route('profil-lembaga.edit') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('profil-lembaga.*') ? $active : $idle }}">
                <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>{{ __('Profil lembaga') }}</span>
            </a>
            <a href="{{ route('pengaturan.presensi.edit') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('pengaturan.presensi.*') ? $active : $idle }}">
                <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>{{ __('Pengaturan presensi') }}</span>
            </a>
        @endif

        @if (auth()->user()->hasRole('pengurus_cabang') && session('pengurus_sekolah_id'))
            <a href="{{ route('pengaturan.presensi.edit') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('pengaturan.presensi.*') ? $active : $idle }}">
                <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>{{ __('Pengaturan presensi') }}</span>
            </a>
        @endif

        @can('viewAny', \App\Models\Sekolah::class)
            <a href="{{ route('pengurus.sekolah.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('pengurus.sekolah.*') ? $active : $idle }}">
                <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>{{ __('Sekolah / PC') }}</span>
            </a>
        @endcan

        @if (auth()->user()->hasAnyRole(['super_admin', 'pengurus_cabang']))
            <a href="{{ route('pengurus.lembaga-registrations.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('pengurus.lembaga-registrations.*') || request()->routeIs('pengurus.lembaga-mou-settings.*') ? $active : $idle }}">
                <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>{{ __('Pendaftaran lembaga') }}</span>
            </a>
        @endif

        @role('wali')
            <div class="px-1 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45" x-show="!sidebarCollapsed" x-cloak>{{ __('Wali Murid') }}</div>
            <a href="{{ route('wali.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('wali.index', 'wali.show') ? $active : $idle }}">
                <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z\"/></svg>
                <span class="flex-1">{{ __('Anak Saya') }}</span>
                <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-nu-gold">{{ __('Wali') }}</span>
            </a>
            @php
                $waliKeuanganSiswa = request()->route('siswa');
            @endphp
            @if ($waliKeuanganSiswa instanceof \App\Models\Siswa && auth()->user()?->waliSiswas()->where('siswas.id', $waliKeuanganSiswa->id)->exists())
                <a href="{{ route('wali.keuangan.dashboard', $waliKeuanganSiswa) }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('wali.keuangan.*', 'wali.tagihan.*') ? $active : $idle }}">
                    <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="flex-1">{{ __('Keuangan anak') }}</span>
                </a>
            @endif
        @endrole

        @role('siswa')
            @can('viewAny', \App\Models\Tugas::class)
                <a href="{{ route('tugas.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('tugas.*') ? $active : $idle }}">
                    <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>{{ __('Tugas') }}</span>
                </a>
            @endcan
            @can('viewAny', \App\Models\PresensiSiswa::class)
                <a href="{{ route('presensi.siswa.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('presensi.siswa.*') ? $active : $idle }}">
                    <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>{{ __('Presensi saya') }}</span>
                </a>
            @endcan
        @endrole

        @if (SidebarNavigation::showModulSection())
        <div class="px-1 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45" x-show="!sidebarCollapsed" x-cloak>{{ __('Modul') }}</div>

        @include('layouts.partials.sidebar-groups.kurikulum', compact('linkBase', 'active', 'idle'))
        @include('layouts.partials.sidebar-groups.siswa', compact('linkBase', 'active', 'idle'))
        @include('layouts.partials.sidebar-groups.guru', compact('linkBase', 'active', 'idle'))

        @can('viewAny', \App\Models\Tagihan::class)
            @php
                $keuanganNavActive = request()->routeIs('keuangan.*', 'tagihan.*', 'pembayaran.*', 'akuntansi.*');
                $keuanganExpanded = $keuanganNavActive ? 'true' : 'false';
            @endphp

            <div
                x-data="{ open: {{ $keuanganExpanded }}, fly: false }"
                class="relative space-y-1"
                @mouseenter="if (sidebarCollapsed) fly = true"
                @mouseleave="fly = false"
            >
                <button
                    type="button"
                    @click="open = !open"
                    class="{{ $linkBase }} w-full {{ $keuanganNavActive ? $active : $idle }}"
                >
                    <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="flex-1 text-left">{{ __('Keuangan') }}</span>
                    <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-nu-gold">{{ __('Admin') }}</span>
                    <svg class="nu-chevron h-4 w-4 shrink-0 text-white/70 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    x-show="fly && sidebarCollapsed"
                    x-transition.opacity.scale.origin.left
                    x-cloak
                    class="absolute left-full top-0 z-50 ml-3 w-64 max-h-[min(70vh,28rem)] overflow-y-auto rounded-2xl border border-gray-200 bg-white text-gray-900 shadow-xl ring-1 ring-black/5"
                >
                    <div class="sticky top-0 z-10 border-b border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Keuangan') }}</div>
                        <div class="mt-0.5 text-sm font-semibold text-gray-900">{{ __('Menu') }}</div>
                    </div>
                    <div class="space-y-3 p-2 pb-3">
                        <div>
                            <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Ringkasan') }}</div>
                            <a href="{{ route('keuangan.index') }}" @click="sidebarOpen=false" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Dashboard keuangan') }}</a>
                        </div>
                        <div>
                            <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Tagihan & bayar') }}</div>
                            <a href="{{ route('tagihan.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Daftar tagihan') }}</a>
                            <a href="{{ route('keuangan.proses.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Proses pembayaran') }}</a>
                            <a href="{{ route('keuangan.tunggakan.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Tunggakan') }}</a>
                            <a href="{{ route('keuangan.rekap.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Rekap keuangan') }}</a>
                        </div>
                        <div>
                            <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Pengaturan') }}</div>
                            <a href="{{ route('keuangan.kewajiban.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Master kewajiban') }}</a>
                        </div>
                        <div>
                            <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Kas') }}</div>
                            <a href="{{ route('keuangan.buku-kas.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Buku kas') }}</a>
                            <a href="{{ route('keuangan.pemasukan-kas.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Pemasukan kas') }}</a>
                            <a href="{{ route('keuangan.pengeluaran-kas.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Pengeluaran kas') }}</a>
                        </div>
                        <div>
                            <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Akuntansi') }}</div>
                            <a href="{{ route('akuntansi.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Jurnal & akuntansi') }}</a>
                            <a href="{{ route('keuangan.coa.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Daftar akun (COA)') }}</a>
                        </div>
                        @if (auth()->user()->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']))
                            <div>
                                <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Ekspor') }}</div>
                                <a href="{{ route('laporan.index') }}" @click="sidebarOpen=false" class="mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Pelaporan / CSV') }}</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div x-show="open && !sidebarCollapsed" x-collapse x-cloak class="space-y-1">
                    <a href="{{ route('keuangan.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('keuangan.index') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Dashboard keuangan') }}</span>
                    </a>
                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white/40" x-show="!sidebarCollapsed" x-cloak>{{ __('Tagihan & bayar') }}</div>
                    <a href="{{ route('tagihan.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('tagihan.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Daftar tagihan') }}</span>
                    </a>
                    <a href="{{ route('keuangan.proses.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('keuangan.proses.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Proses pembayaran') }}</span>
                    </a>
                    <a href="{{ route('keuangan.tunggakan.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('keuangan.tunggakan.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Tunggakan') }}</span>
                    </a>
                    <a href="{{ route('keuangan.rekap.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('keuangan.rekap.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Rekap keuangan') }}</span>
                    </a>
                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white/40" x-show="!sidebarCollapsed" x-cloak>{{ __('Pengaturan') }}</div>
                    <a href="{{ route('keuangan.kewajiban.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('keuangan.kewajiban.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Master kewajiban') }}</span>
                    </a>
                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white/40" x-show="!sidebarCollapsed" x-cloak>{{ __('Kas') }}</div>
                    <a href="{{ route('keuangan.buku-kas.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('keuangan.buku-kas.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Buku kas') }}</span>
                    </a>
                    <a href="{{ route('keuangan.pemasukan-kas.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('keuangan.pemasukan-kas.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Pemasukan kas') }}</span>
                    </a>
                    <a href="{{ route('keuangan.pengeluaran-kas.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('keuangan.pengeluaran-kas.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Pengeluaran kas') }}</span>
                    </a>
                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white/40" x-show="!sidebarCollapsed" x-cloak>{{ __('Akuntansi') }}</div>
                    <a href="{{ route('akuntansi.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('akuntansi.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Jurnal & akuntansi') }}</span>
                    </a>
                    <a href="{{ route('keuangan.coa.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('keuangan.coa.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Daftar akun (COA)') }}</span>
                    </a>
                    @if (auth()->user()->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']))
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white/40" x-show="!sidebarCollapsed" x-cloak>{{ __('Ekspor') }}</div>
                        <a href="{{ route('laporan.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('laporan.*') ? $active : $idle }}">
                            <span class="flex-1">{{ __('Pelaporan / CSV') }}</span>
                        </a>
                    @endif
                </div>
            </div>
        @endcan

        @can('viewAny', \App\Models\Berita::class)
            <a href="{{ route('berita.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('berita.*') ? $active : $idle }}">
                <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span class="flex-1">{{ __('Berita') }}</span>
                <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-nu-gold">{{ __('Admin') }}</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\PerpustakaanBuku::class)
            @php $perpusExpanded = request()->routeIs('perpustakaan.*') ? 'true' : 'false'; @endphp
            <div x-data="{ open: {{ $perpusExpanded }}, fly: false }" class="relative space-y-1" @mouseenter="if (sidebarCollapsed) fly = true" @mouseleave="fly = false">
                <button type="button" @click="open = !open" class="{{ $linkBase }} w-full {{ request()->routeIs('perpustakaan.*') ? $active : $idle }}">
                    <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span class="flex-1 text-left">{{ __('Perpustakaan') }}</span>
                    <svg class="nu-chevron h-4 w-4 shrink-0 text-white/70 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open && !sidebarCollapsed" x-collapse x-cloak class="space-y-1">
                    <a href="{{ route('perpustakaan.dashboard') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('perpustakaan.dashboard') ? $active : $idle }}"><span>{{ __('Ringkasan') }}</span></a>
                    <a href="{{ route('perpustakaan.buku.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('perpustakaan.buku.*') ? $active : $idle }}"><span>{{ __('Katalog') }}</span></a>
                    <a href="{{ route('perpustakaan.peminjaman.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('perpustakaan.peminjaman.*') ? $active : $idle }}"><span>{{ __('Peminjaman') }}</span></a>
                    @can('viewAny', \App\Models\PerpustakaanKategori::class)
                        <a href="{{ route('perpustakaan.kategori.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('perpustakaan.kategori.*') ? $active : $idle }}"><span>{{ __('Kategori') }}</span></a>
                        <a href="{{ route('perpustakaan.pengaturan.edit') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('perpustakaan.pengaturan.*') ? $active : $idle }}"><span>{{ __('Pengaturan') }}</span></a>
                    @endcan
                </div>
            </div>
        @endcan

        @can('viewAny', \App\Models\InventarisBarang::class)
            @php
                $inventarisExpanded = request()->routeIs('inventaris.*') ? 'true' : 'false';
            @endphp

            <div
                x-data="{ open: {{ $inventarisExpanded }}, fly: false }"
                class="relative space-y-1"
                @mouseenter="if (sidebarCollapsed) fly = true"
                @mouseleave="fly = false"
            >
                <button
                    type="button"
                    @click="open = !open"
                    class="{{ $linkBase }} w-full {{ request()->routeIs('inventaris.*') ? $active : $idle }}"
                >
                    <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4m6-8h4"/></svg>
                    <span class="flex-1 text-left">{{ __('Inventaris') }}</span>
                    <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-nu-gold">{{ __('Stok') }}</span>
                    <svg class="nu-chevron h-4 w-4 shrink-0 text-white/70 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    x-show="fly && sidebarCollapsed"
                    x-transition.opacity.scale.origin.left
                    x-cloak
                    class="absolute left-full top-0 z-50 ml-3 w-56 overflow-hidden rounded-2xl border border-gray-200 bg-white text-gray-900 shadow-xl ring-1 ring-black/5"
                >
                    <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Inventaris') }}</div>
                        <div class="mt-0.5 text-sm font-semibold text-gray-900">{{ __('Menu') }}</div>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('inventaris.barang.index') }}" @click="sidebarOpen=false" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            <span class="flex-1">{{ __('Barang') }}</span>
                        </a>
                        <a href="{{ route('inventaris.mutasi.index') }}" @click="sidebarOpen=false" class="mt-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            <span class="flex-1">{{ __('Mutasi') }}</span>
                        </a>
                        @can('viewAny', \App\Models\InventarisKategori::class)
                            <a href="{{ route('inventaris.kategori.index') }}" @click="sidebarOpen=false" class="mt-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                <span class="flex-1">{{ __('Kategori') }}</span>
                            </a>
                        @endcan
                    </div>
                </div>

                <div x-show="open && !sidebarCollapsed" x-collapse x-cloak class="space-y-1">
                    <a href="{{ route('inventaris.barang.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('inventaris.barang.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Barang') }}</span>
                    </a>
                    <a href="{{ route('inventaris.mutasi.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('inventaris.mutasi.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Mutasi') }}</span>
                    </a>
                    @can('viewAny', \App\Models\InventarisKategori::class)
                        <a href="{{ route('inventaris.kategori.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('inventaris.kategori.*') ? $active : $idle }}">
                            <span class="flex-1">{{ __('Kategori') }}</span>
                        </a>
                    @endcan
                </div>
            </div>
        @endcan

        @if (auth()->user()->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']))
            <a href="{{ route('laporan.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('laporan.*') ? $active : $idle }}">
                <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12h3m3 0h3M7 8h10M7 16h6m-6 4h6a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="flex-1">{{ __('Laporan') }}</span>
                <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-nu-gold">{{ __('Ekspor') }}</span>
            </a>
        @endif

        @can('viewAny', \App\Models\Pelanggaran::class)
            @php
                $bkNavActive = request()->routeIs('bk.*', 'kesiswaan.reward.*');
                $bkExpanded = $bkNavActive ? 'true' : 'false';
            @endphp

            <div
                x-data="{ open: {{ $bkExpanded }}, fly: false }"
                class="relative space-y-1"
                @mouseenter="if (sidebarCollapsed) fly = true"
                @mouseleave="fly = false"
            >
                <button
                    type="button"
                    @click="open = !open"
                    class="{{ $linkBase }} w-full {{ $bkNavActive ? $active : $idle }}"
                >
                    <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="flex-1 text-left">{{ __('BK') }}</span>
                    <svg class="nu-chevron h-4 w-4 shrink-0 text-white/70 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    x-show="fly && sidebarCollapsed"
                    x-transition.opacity.scale.origin.left
                    x-cloak
                    class="absolute left-full top-0 z-50 ml-3 w-56 overflow-hidden rounded-2xl border border-gray-200 bg-white text-gray-900 shadow-xl ring-1 ring-black/5"
                >
                    <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('BK') }}</div>
                        <div class="mt-0.5 text-sm font-semibold text-gray-900">{{ __('Menu') }}</div>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('bk.dashboard') }}" @click="sidebarOpen=false" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Dashboard BK') }}</a>
                        <a href="{{ route('bk.pelanggaran.index') }}" @click="sidebarOpen=false" class="mt-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Pelanggaran') }}</a>
                        <a href="{{ route('bk.jenis-pelanggaran.index') }}" @click="sidebarOpen=false" class="mt-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Jenis Pelanggaran') }}</a>
                        <a href="{{ route('bk.sanksi.index') }}" @click="sidebarOpen=false" class="mt-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Sanksi') }}</a>
                        <a href="{{ route('kesiswaan.reward.index') }}" @click="sidebarOpen=false" class="mt-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Reward') }}</a>
                        <a href="{{ route('bk.pemanggilan.index') }}" @click="sidebarOpen=false" class="mt-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Pemanggilan') }}</a>
                        <a href="{{ route('bk.home-visit.index') }}" @click="sidebarOpen=false" class="mt-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Home Visit') }}</a>
                    </div>
                </div>

                <div x-show="open && !sidebarCollapsed" x-collapse x-cloak class="space-y-1">
                    <a href="{{ route('bk.dashboard') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('bk.dashboard') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Dashboard BK') }}</span>
                    </a>
                    <a href="{{ route('bk.pelanggaran.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('bk.pelanggaran.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Pelanggaran') }}</span>
                    </a>
                    <a href="{{ route('bk.jenis-pelanggaran.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('bk.jenis-pelanggaran.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Jenis Pelanggaran') }}</span>
                    </a>
                    <a href="{{ route('bk.sanksi.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('bk.sanksi.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Sanksi') }}</span>
                    </a>
                    <a href="{{ route('kesiswaan.reward.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('kesiswaan.reward.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Reward') }}</span>
                    </a>
                    <a href="{{ route('bk.pemanggilan.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('bk.pemanggilan.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Pemanggilan') }}</span>
                    </a>
                    <a href="{{ route('bk.home-visit.index') }}" @click="sidebarOpen=false" class="{{ $linkBase }} ml-6 {{ request()->routeIs('bk.home-visit.*') ? $active : $idle }}">
                        <span class="flex-1">{{ __('Home Visit') }}</span>
                    </a>
                </div>
            </div>
        @endcan
        @endif
    </nav>

    <div class="shrink-0 border-t border-white/10 p-3">
        <a href="{{ route('profile.edit') }}" @click="sidebarOpen=false" class="{{ $linkBase }} {{ request()->routeIs('profile.*') ? $active : $idle }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>{{ __('Profil') }}</span>
        </a>
    </div>
</div>
