<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('BK — Dashboard') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('Ringkasan modul bimbingan konseling dan kesiswaan.') }}</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Pelanggaran') }}</div>
                <div class="mt-1 text-2xl font-extrabold text-nu-primary">{{ $stats['pelanggaran'] }}</div>
            </div>
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Jenis aktif') }}</div>
                <div class="mt-1 text-2xl font-extrabold text-gray-900">{{ $stats['jenis'] }}</div>
            </div>
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sanksi aktif') }}</div>
                <div class="mt-1 text-2xl font-extrabold text-gray-900">{{ $stats['sanksi'] }}</div>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 ring-1 ring-amber-100">
                <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">{{ __('Pemanggilan terjadwal') }}</div>
                <div class="mt-1 text-2xl font-extrabold text-amber-900">{{ $stats['pemanggilan'] }}</div>
            </div>
            <div class="rounded-2xl border border-violet-100 bg-violet-50 p-4 ring-1 ring-violet-100">
                <div class="text-xs font-semibold uppercase tracking-wide text-violet-700">{{ __('Home visit belum dilaporkan') }}</div>
                <div class="mt-1 text-2xl font-extrabold text-violet-900">{{ $stats['home_visit'] }}</div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('bk.pelanggaran.index') }}" class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:bg-gray-50">
                <div class="text-sm font-bold text-nu-primary">{{ __('Pelanggaran') }}</div>
                <p class="mt-1 text-xs text-gray-600">{{ __('Catat dan kelola pelanggaran siswa.') }}</p>
            </a>
            <a href="{{ route('bk.jenis-pelanggaran.index') }}" class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:bg-gray-50">
                <div class="text-sm font-bold text-nu-primary">{{ __('Jenis pelanggaran') }}</div>
                <p class="mt-1 text-xs text-gray-600">{{ __('Master jenis dan poin pelanggaran.') }}</p>
            </a>
            <a href="{{ route('bk.sanksi.index') }}" class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:bg-gray-50">
                <div class="text-sm font-bold text-nu-primary">{{ __('Sanksi') }}</div>
                <p class="mt-1 text-xs text-gray-600">{{ __('Master sanksi dan tingkat.') }}</p>
            </a>
            <a href="{{ route('bk.pemanggilan.index') }}" class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:bg-gray-50">
                <div class="text-sm font-bold text-nu-primary">{{ __('Pemanggilan') }}</div>
                <p class="mt-1 text-xs text-gray-600">{{ __('Jadwal pemanggilan siswa / wali.') }}</p>
            </a>
            <a href="{{ route('bk.home-visit.index') }}" class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:bg-gray-50">
                <div class="text-sm font-bold text-nu-primary">{{ __('Home visit') }}</div>
                <p class="mt-1 text-xs text-gray-600">{{ __('Kunjungan rumah dan laporan ke kepala sekolah.') }}</p>
            </a>
            <a href="{{ route('kesiswaan.reward.index') }}" class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:bg-gray-50">
                <div class="text-sm font-bold text-nu-primary">{{ __('Reward siswa') }}</div>
                <p class="mt-1 text-xs text-gray-600">{{ __('Poin positif prestasi dan administrasi.') }}</p>
            </a>
            <a href="{{ route('kesiswaan.lomba.index') }}" class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:bg-gray-50">
                <div class="text-sm font-bold text-nu-primary">{{ __('Lomba / ajang') }}</div>
                <p class="mt-1 text-xs text-gray-600">{{ __('Prestasi siswa di lomba eksternal.') }}</p>
            </a>
            <a href="{{ route('kesiswaan.ekstrakurikuler.index') }}" class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:bg-gray-50">
                <div class="text-sm font-bold text-nu-primary">{{ __('Ekstrakurikuler') }}</div>
                <p class="mt-1 text-xs text-gray-600">{{ __('Kegiatan ekskul dan anggota.') }}</p>
            </a>
            <a href="{{ route('kesiswaan.kokurikuler.index') }}" class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:bg-gray-50">
                <div class="text-sm font-bold text-nu-primary">{{ __('Kokurikuler') }}</div>
                <p class="mt-1 text-xs text-gray-600">{{ __('Kegiatan kokurikuler dan LKPD.') }}</p>
            </a>
        </div>
    </div>
</x-app-layout>
