<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Absensi') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Pilih jenis presensi yang ingin dikelola.') }}</p>
        </div>
    </x-slot>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('presensi.siswa.index') }}" class="group rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:ring-nu-primary/25">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-nu-primary/10 text-nu-primary ring-1 ring-nu-primary/15">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 group-hover:text-nu-primary">{{ __('Presensi siswa') }}</div>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Catat kehadiran per kelas — manual, barcode, atau wajah.') }}</p>
                    <div class="mt-3 flex flex-wrap gap-2 text-sm font-semibold">
                        <span class="text-nu-primary group-hover:underline">{{ __('Buka daftar') }} →</span>
                        @can('create', \App\Models\PresensiSiswa::class)
                            <a href="{{ route('presensi.scan.show', 'siswa') }}" class="text-gray-600 hover:text-nu-primary" onclick="event.stopPropagation()">{{ __('Scan') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('presensi.guru.index') }}" class="group rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:ring-nu-primary/25">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-nu-primary/10 text-nu-primary ring-1 ring-nu-primary/15">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 group-hover:text-nu-primary">{{ __('Presensi guru') }}</div>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Catat kehadiran staf pengajar — manual, barcode, atau wajah.') }}</p>
                    <div class="mt-3 flex flex-wrap gap-2 text-sm font-semibold">
                        <span class="text-nu-primary group-hover:underline">{{ __('Buka daftar') }} →</span>
                        @can('create', \App\Models\PresensiGuru::class)
                            <a href="{{ route('presensi.scan.show', 'guru') }}" class="text-gray-600 hover:text-nu-primary" onclick="event.stopPropagation()">{{ __('Scan') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('presensi.pegawai.index') }}" class="group rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:ring-nu-primary/25">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-nu-primary/10 text-nu-primary ring-1 ring-nu-primary/15">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 group-hover:text-nu-primary">{{ __('Presensi pegawai') }}</div>
                    <p class="mt-1 text-sm text-gray-600">{{ __('TU, keamanan — manual, barcode, atau wajah.') }}</p>
                    <div class="mt-3 flex flex-wrap gap-2 text-sm font-semibold">
                        <span class="text-nu-primary group-hover:underline">{{ __('Buka daftar') }} →</span>
                        @can('create', \App\Models\PresensiPegawai::class)
                            <a href="{{ route('presensi.scan.show', 'pegawai') }}" class="text-gray-600 hover:text-nu-primary" onclick="event.stopPropagation()">{{ __('Scan') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </a>
    </div>
</x-app-layout>
