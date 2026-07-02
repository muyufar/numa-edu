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
                    @php
                        $tungg = $tunggakanBySiswa[$s->id] ?? null;
                        $adaTunggakan = $tungg && $tungg['count'] > 0;
                    @endphp
                    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-md">
                        <a href="{{ route('wali.show', $s) }}" class="block p-6 pb-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Siswa') }}</div>
                            <div class="mt-1 text-lg font-extrabold text-gray-900">{{ $s->nama }}</div>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold text-gray-600">
                                <span class="rounded-full bg-gray-50 px-3 py-1 ring-1 ring-gray-100">{{ __('NIS') }}: {{ $s->nis }}</span>
                                @if ($s->kelas)
                                    <span class="rounded-full bg-gray-50 px-3 py-1 ring-1 ring-gray-100">{{ __('Kelas') }}: {{ $s->kelas->tingkat }} {{ $s->kelas->nama }}</span>
                                @endif
                            </div>
                        </a>
                        <div class="border-t border-gray-100 px-6 py-4">
                            @if ($adaTunggakan)
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-amber-900">{{ __('Ada tunggakan') }}</span>
                                        <p class="mt-1 text-xs text-gray-600">
                                            {{ trans_choice(':count tagihan|:count tagihan', $tungg['count'], ['count' => $tungg['count']]) }}
                                            · @include('keuangan.partials.rupiah', ['value' => $tungg['total_sisa'], 'decimals' => 0])
                                        </p>
                                    </div>
                                    <a href="{{ route('wali.keuangan.dashboard', $s) }}" class="text-sm font-bold text-nu-primary hover:underline">{{ __('Keuangan') }} →</a>
                                </div>
                            @else
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-emerald-800">{{ __('Tidak ada tunggakan') }}</span>
                                    <a href="{{ route('wali.keuangan.dashboard', $s) }}" class="text-sm font-bold text-nu-primary hover:underline">{{ __('Keuangan') }} →</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-gray-100 bg-white p-8 text-center text-sm text-gray-600 shadow-sm ring-1 ring-black/5">
                        {{ __('Belum ada siswa yang ditautkan ke akun wali ini.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
