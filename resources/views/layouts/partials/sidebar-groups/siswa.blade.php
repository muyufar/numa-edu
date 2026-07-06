@php
    $siswaActive = request()->routeIs('siswa.*', 'wali-admin.*', 'ppdb.*', 'presensi.siswa.*', 'perizinan.*');
    $siswaExpanded = $siswaActive ? 'true' : 'false';
    $subLink = fn (bool $isActive) => $linkBase.' ml-6 '.($isActive ? $active : $idle);
    $flyLink = 'mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50';
@endphp

@if (\App\Support\SidebarNavigation::showSiswaMenu())
    <x-sidebar-group
        :label="__('Siswa')"
        :group-active="$siswaActive"
        :expanded="$siswaExpanded"
        :badge="__('Master')"
        :link-base="$linkBase"
        :active-class="$active"
        :idle-class="$idle"
    >
        <x-slot:icon>
            <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </x-slot:icon>

        <x-slot:flyout>
            @can('viewAny', \App\Models\Siswa::class)
                <a href="{{ route('siswa.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Daftar siswa') }}</a>
            @endcan
            @can('viewAny', \App\Models\PpdbRegistration::class)
                <a href="{{ route('ppdb.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('PPDB') }}</a>
            @endcan
            @if (auth()->user()->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang']))
                <a href="{{ route('wali-admin.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Admin wali') }}</a>
            @endif
            @can('viewAny', \App\Models\PresensiSiswa::class)
                <a href="{{ route('presensi.siswa.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Presensi siswa') }}</a>
            @endcan
            @can('viewAny', \App\Models\Siswa::class)
                <a href="{{ route('siswa.alumni.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Daftar alumni') }}</a>
            @endcan
            @can('viewAny', \App\Models\Perizinan::class)
                <a href="{{ route('perizinan.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Perizinan') }}</a>
            @endcan
        </x-slot:flyout>

        @can('viewAny', \App\Models\Siswa::class)
            <a href="{{ route('siswa.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('siswa.index', 'siswa.create', 'siswa.edit')) }}"><span class="flex-1">{{ __('Daftar siswa') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\PpdbRegistration::class)
            <a href="{{ route('ppdb.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('ppdb.*')) }}"><span class="flex-1">{{ __('PPDB') }}</span></a>
        @endcan
        @if (auth()->user()->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang']))
            <a href="{{ route('wali-admin.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('wali-admin.*')) }}"><span class="flex-1">{{ __('Admin wali') }}</span></a>
        @endif
        @can('viewAny', \App\Models\PresensiSiswa::class)
            <a href="{{ route('presensi.siswa.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('presensi.siswa.*')) }}"><span class="flex-1">{{ __('Presensi siswa') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\Siswa::class)
            <a href="{{ route('siswa.alumni.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('siswa.alumni.*')) }}"><span class="flex-1">{{ __('Daftar alumni') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\Perizinan::class)
            <a href="{{ route('perizinan.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('perizinan.*')) }}"><span class="flex-1">{{ __('Perizinan') }}</span></a>
        @endcan
    </x-sidebar-group>
@endif
