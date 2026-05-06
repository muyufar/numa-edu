<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Rekap keuangan') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Ringkasan tagihan, pembayaran, dan piutang per periode.') }}</p>
            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs font-semibold">
                <a href="{{ route('keuangan.index') }}" class="text-nu-primary hover:underline">{{ __('Hub keuangan') }}</a>
                <a href="{{ route('keuangan.tunggakan.index', array_filter(['periode_from' => $periodeFrom, 'periode_to' => $periodeTo, 'kelas_id' => $kelasId], fn ($v) => $v !== null && $v !== '')) }}" class="text-orange-800 hover:underline">{{ __('Tunggakan') }}</a>
                <a href="{{ route('laporan.index', array_filter(['periode_from' => $periodeFrom, 'periode_to' => $periodeTo, 'kelas_id' => $kelasId], fn ($v) => $v !== null && $v !== '')) }}" class="text-sky-800 hover:underline">{{ __('Pelaporan / CSV') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
            <div class="text-sm font-bold text-gray-900">{{ __('Filter') }}</div>
            <form method="GET" action="{{ route('keuangan.rekap.index') }}" class="mt-4 grid gap-4 sm:grid-cols-12">
                @php
                    $selectBase = 'w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
                @endphp

                <div class="sm:col-span-6 lg:col-span-2 space-y-2">
                    <x-input-label for="periode_from" :value="__('Periode dari')" />
                    <x-text-input id="periode_from" name="periode_from" class="block w-full font-mono" type="text" maxlength="7" :value="$periodeFrom" placeholder="2026-01" />
                </div>
                <div class="sm:col-span-6 lg:col-span-2 space-y-2">
                    <x-input-label for="periode_to" :value="__('Periode sampai')" />
                    <x-text-input id="periode_to" name="periode_to" class="block w-full font-mono" type="text" maxlength="7" :value="$periodeTo" placeholder="2026-12" />
                </div>
                <div class="sm:col-span-12 lg:col-span-4 space-y-2">
                    <x-input-label for="kelas_id" :value="__('Kelas (opsional)')" />
                    <select id="kelas_id" name="kelas_id" class="{{ $selectBase }}">
                        <option value="">{{ __('Semua kelas') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" @selected((string) $kelasId === (string) $k->id)>{{ $k->tingkat }} {{ $k->nama }} {{ $k->tahun_ajaran }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-12 lg:col-span-2 flex items-end">
                    <x-primary-button class="w-full justify-center">{{ __('Terapkan') }}</x-primary-button>
                </div>
                <div class="sm:col-span-12 lg:col-span-2 flex items-end">
                    <a href="{{ route('keuangan.rekap.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total tagihan') }}</div>
                <div class="mt-1 font-mono text-2xl font-extrabold text-gray-900">@include('keuangan.partials.rupiah', ['value' => $totalTagihan])</div>
                <div class="mt-1 text-xs font-mono text-gray-500">{{ $periodeFrom }} → {{ $periodeTo }}</div>
            </div>
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total dibayar') }}</div>
                <div class="mt-1 font-mono text-2xl font-extrabold text-emerald-700">@include('keuangan.partials.rupiah', ['value' => $totalDibayar])</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('Cross-check pembayaran') }}: @include('keuangan.partials.rupiah', ['value' => $pemasukan])</div>
            </div>
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sisa piutang') }}</div>
                <div class="mt-1 font-mono text-2xl font-extrabold text-nu-primary">@include('keuangan.partials.rupiah', ['value' => $totalSisa])</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('unpaid + partial') }}</div>
            </div>
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status tagihan') }}</div>
                <div class="mt-2 flex flex-wrap gap-2 text-xs font-bold">
                    <span class="rounded-full bg-red-50 px-2 py-1 text-red-800">unpaid: {{ $byStatus['unpaid'] }}</span>
                    <span class="rounded-full bg-amber-50 px-2 py-1 text-amber-800">partial: {{ $byStatus['partial'] }}</span>
                    <span class="rounded-full bg-emerald-50 px-2 py-1 text-emerald-800">paid: {{ $byStatus['paid'] }}</span>
                </div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
                <div class="border-b border-gray-100 px-5 py-4 text-sm font-bold text-gray-900">{{ __('Piutang per siswa (top 50)') }}</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3">{{ __('Siswa') }}</th>
                                <th class="px-5 py-3">{{ __('Kelas') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Sisa') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($piutangSiswa as $row)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-5 py-3 font-medium text-gray-900">
                                        @if($row['siswa'])
                                            <a class="hover:underline" href="{{ route('keuangan.rekap.siswa', ['siswa' => $row['siswa']->id, 'periode_from' => $periodeFrom, 'periode_to' => $periodeTo]) }}">
                                                {{ $row['siswa']?->nama }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                        <div class="mt-0.5 font-mono text-xs text-gray-500">{{ $row['siswa']?->nis ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-700">
                                        @php $k = $row['siswa']?->kelas; @endphp
                                        {{ $k ? "{$k->tingkat} {$k->nama} {$k->tahun_ajaran}" : '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-mono font-bold text-nu-primary">@include('keuangan.partials.rupiah', ['value' => $row['sisa']])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Tidak ada piutang pada filter ini.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
                <div class="border-b border-gray-100 px-5 py-4 text-sm font-bold text-gray-900">{{ __('Piutang per kelas') }}</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3">{{ __('Kelas') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Sisa') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($piutangKelas as $row)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-5 py-3 font-medium text-gray-900">
                                        @php $k = $row['kelas']; @endphp
                                        @if($k)
                                            <a class="hover:underline" href="{{ route('keuangan.rekap.kelas', ['kelas' => $k->id, 'periode_from' => $periodeFrom, 'periode_to' => $periodeTo]) }}">
                                                {{ $k->tingkat }} {{ $k->nama }} {{ $k->tahun_ajaran }}
                                            </a>
                                        @else
                                            {{ __('Tanpa kelas') }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right font-mono font-bold text-nu-primary">@include('keuangan.partials.rupiah', ['value' => $row['sisa']])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Tidak ada data.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

