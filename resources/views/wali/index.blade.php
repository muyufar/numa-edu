<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-extrabold text-gray-900">{{ __('Anak Saya') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Ringkasan data siswa yang ditautkan ke akun wali.') }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($siswas as $s)
                    <a href="{{ route('wali.show', $s) }}" class="block rounded-3xl border border-gray-100 bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Siswa') }}</div>
                        <div class="mt-1 text-lg font-extrabold text-gray-900">{{ $s->nama }}</div>
                        <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold text-gray-600">
                            <span class="rounded-full bg-gray-50 px-3 py-1 ring-1 ring-gray-100">{{ __('NIS') }}: {{ $s->nis }}</span>
                            @if ($s->kelas)
                                <span class="rounded-full bg-gray-50 px-3 py-1 ring-1 ring-gray-100">{{ __('Kelas') }}: {{ $s->kelas->tingkat }} {{ $s->kelas->nama }}</span>
                            @endif
                        </div>
                        <div class="mt-4 text-sm font-bold text-nu-primary">{{ __('Lihat detail') }} →</div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-gray-100 bg-white p-8 text-center text-sm text-gray-600 shadow-sm ring-1 ring-black/5">
                        {{ __('Belum ada siswa yang ditautkan ke akun wali ini.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

