@php
    $kurikulumActive = request()->routeIs('kelas.*', 'mapel.*', 'kurikulum.*', 'jadwal.*', 'nilai.*', 'materi.*', 'tugas.*', 'kesiswaan.lomba.*', 'kesiswaan.ekstrakurikuler.*', 'kesiswaan.kokurikuler.*');
    $kurikulumExpanded = $kurikulumActive ? 'true' : 'false';
    $subLink = fn (bool $isActive) => $linkBase.' ml-6 '.($isActive ? $active : $idle);
    $flyLink = 'flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50';
@endphp

@if (\App\Support\SidebarNavigation::showKurikulumMenu())
    <x-sidebar-group
        :label="__('Kurikulum')"
        :group-active="$kurikulumActive"
        :expanded="$kurikulumExpanded"
        :badge="__('Akademik')"
        :link-base="$linkBase"
        :active-class="$active"
        :idle-class="$idle"
    >
        <x-slot:icon>
            <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </x-slot:icon>

        <x-slot:flyout>
            @can('viewAny', \App\Models\Kelas::class)
                <a href="{{ route('kelas.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Kelas') }}</a>
            @endcan
            @can('viewAny', \App\Models\MataPelajaran::class)
                <a href="{{ route('mapel.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Mapel') }}</a>
            @endcan
            @can('viewAny', \App\Models\KurikulumItem::class)
                <a href="{{ route('kurikulum.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Kurikulum') }}</a>
            @endcan
            @can('viewAny', \App\Models\Jadwal::class)
                <a href="{{ route('jadwal.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Jadwal') }}</a>
            @endcan
            @can('viewAny', \App\Models\Nilai::class)
                <a href="{{ route('nilai.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Nilai') }}</a>
            @endcan
            @can('viewAny', \App\Models\MateriAjar::class)
                <a href="{{ route('materi.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Perangkat ajar') }}</a>
            @endcan
            @can('viewAny', \App\Models\Tugas::class)
                <a href="{{ route('tugas.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Tugas') }}</a>
            @endcan
            @can('viewAny', \App\Models\LombaAjang::class)
                <a href="{{ route('kesiswaan.lomba.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Lomba') }}</a>
            @endcan
            @can('viewAny', \App\Models\Ekstrakurikuler::class)
                <a href="{{ route('kesiswaan.ekstrakurikuler.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Ekstrakurikuler') }}</a>
            @endcan
            @can('viewAny', \App\Models\KokurikulerKegiatan::class)
                <a href="{{ route('kesiswaan.kokurikuler.index') }}" @click="sidebarOpen=false" class="{{ $flyLink }}">{{ __('Kokurikuler') }}</a>
            @endcan
        </x-slot:flyout>

        @can('viewAny', \App\Models\Kelas::class)
            <a href="{{ route('kelas.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('kelas.*')) }}"><span class="flex-1">{{ __('Kelas') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\MataPelajaran::class)
            <a href="{{ route('mapel.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('mapel.*')) }}"><span class="flex-1">{{ __('Mapel') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\KurikulumItem::class)
            <a href="{{ route('kurikulum.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('kurikulum.*')) }}"><span class="flex-1">{{ __('Kurikulum') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\Jadwal::class)
            <a href="{{ route('jadwal.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('jadwal.*')) }}"><span class="flex-1">{{ __('Jadwal') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\Nilai::class)
            <a href="{{ route('nilai.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('nilai.*')) }}"><span class="flex-1">{{ __('Nilai') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\MateriAjar::class)
            <a href="{{ route('materi.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('materi.*')) }}"><span class="flex-1">{{ __('Perangkat ajar') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\Tugas::class)
            <a href="{{ route('tugas.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('tugas.*')) }}"><span class="flex-1">{{ __('Tugas') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\LombaAjang::class)
            <a href="{{ route('kesiswaan.lomba.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('kesiswaan.lomba.*')) }}"><span class="flex-1">{{ __('Lomba') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\Ekstrakurikuler::class)
            <a href="{{ route('kesiswaan.ekstrakurikuler.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('kesiswaan.ekstrakurikuler.*')) }}"><span class="flex-1">{{ __('Ekstrakurikuler') }}</span></a>
        @endcan
        @can('viewAny', \App\Models\KokurikulerKegiatan::class)
            <a href="{{ route('kesiswaan.kokurikuler.index') }}" @click="sidebarOpen=false" class="{{ $subLink(request()->routeIs('kesiswaan.kokurikuler.*')) }}"><span class="flex-1">{{ __('Kokurikuler') }}</span></a>
        @endcan
    </x-sidebar-group>
@endif
