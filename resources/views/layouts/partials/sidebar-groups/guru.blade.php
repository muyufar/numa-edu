@php
    $guruActive = request()->routeIs('tenaga-kependidikan.*', 'guru.*', 'pegawai.*', 'kinerja.*', 'presensi.guru.*', 'presensi.pegawai.*');
    $guruExpanded = $guruActive ? 'true' : 'false';
    $tenagaKependidikanDataActive = request()->routeIs('tenaga-kependidikan.*')
        || (request()->routeIs('guru.*') && ! request()->routeIs('presensi.guru.*'))
        || (request()->routeIs('pegawai.*') && ! request()->routeIs('presensi.pegawai.*'));
    $subLink = fn (bool $isActive) => $linkBase.' ml-6 '.($isActive ? $active : $idle);
    $flyLink = 'mt-0.5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50';
@endphp

@if (\App\Support\SidebarNavigation::showGuruMenu())
    <x-sidebar-group
        :label="__('Guru dan Tendik')"
        :group-active="$guruActive"
        :expanded="$guruExpanded"
        :badge="__('SDM')"
        :link-base="$linkBase"
        :active-class="$active"
        :idle-class="$idle"
    >
        <x-slot:icon>
            <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </x-slot:icon>

        <x-slot:flyout>
            @if (auth()->user()->can('viewAny', \App\Models\Guru::class) || auth()->user()->can('viewAny', \App\Models\Pegawai::class))
                <a href="{{ route('tenaga-kependidikan.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Daftar GTK') }}</a>
            @endif
            @can('viewAny', \App\Models\KinerjaPenilaian::class)
                <a href="{{ route('kinerja.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Kinerja') }}</a>
            @endcan
            @can('viewAny', \App\Models\PresensiGuru::class)
                <a href="{{ route('presensi.guru.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Presensi guru') }}</a>
            @endcan
            @can('viewAny', \App\Models\PresensiPegawai::class)
                <a href="{{ route('presensi.pegawai.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Presensi pegawai') }}</a>
            @endcan
        </x-slot:flyout>

        @if (auth()->user()->can('viewAny', \App\Models\Guru::class) || auth()->user()->can('viewAny', \App\Models\Pegawai::class))
            <a href="{{ route('tenaga-kependidikan.index') }}" @click="sidebarOpen=false" class="{{ $subLink($tenagaKependidikanDataActive) }}"><span class="flex-1">{{ __('Daftar GTK') }}</span></a>
        @endif
        @can('viewAny', \App\Models\KinerjaPenilaian::class)
            <a href="{{ route('kinerja.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('kinerja.*')) }}"><span class="flex-1">{{ __('Kinerja') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\PresensiGuru::class)
            <a href="{{ route('presensi.guru.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('presensi.guru.*')) }}"><span class="flex-1">{{ __('Presensi guru') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\PresensiPegawai::class)
            <a href="{{ route('presensi.pegawai.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('presensi.pegawai.*')) }}"><span class="flex-1">{{ __('Presensi pegawai') }}</span></a>
        @endcan
    </x-sidebar-group>
@endif
