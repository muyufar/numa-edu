<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Tunggakan') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Tagihan berstatus belum lunas atau sebagian, dengan sisa piutang.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('keuangan.index') }}" class="btn-nu">{{ __('Keuangan') }}</a>
                <a href="{{ route('keuangan.tunggakan.export', request()->query()) }}" class="btn-nu-primary">{{ __('Unduh CSV') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="mb-4 grid gap-3 sm:grid-cols-2">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Jumlah baris tunggakan') }}</div>
            <div class="mt-1 text-2xl font-extrabold text-gray-900">{{ number_format($jumlahTunggakan) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total sisa (filter)') }}</div>
            <div class="mt-1 text-2xl font-extrabold text-amber-800">Rp {{ number_format((int) round($totalSisa), 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
        <form method="GET" action="{{ route('keuangan.tunggakan.index') }}" class="grid gap-4 sm:grid-cols-12 sm:items-end">
            <div class="sm:col-span-3">
                <x-input-label for="periode_from" :value="__('Periode dari')" />
                <x-text-input id="periode_from" name="periode_from" class="mt-2 block w-full font-mono" type="month" :value="$periodeFrom" />
            </div>
            <div class="sm:col-span-3">
                <x-input-label for="periode_to" :value="__('Periode sampai')" />
                <x-text-input id="periode_to" name="periode_to" class="mt-2 block w-full font-mono" type="month" :value="$periodeTo" />
            </div>
            <div class="sm:col-span-3">
                <x-input-label for="kelas_id" :value="__('Kelas')" />
                <select id="kelas_id" name="kelas_id" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                    <option value="">{{ __('Semua kelas') }}</option>
                    @foreach ($kelasOptions as $k)
                        <option value="{{ $k->id }}" @selected((string) $kelasId === (string) $k->id)>
                            {{ trim("{$k->tingkat} {$k->nama} {$k->tahun_ajaran}") }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-3">
                <x-input-label for="min_sisa" :value="__('Min. sisa (Rp)')" />
                <x-text-input id="min_sisa" name="min_sisa" class="mt-2 block w-full" type="number" step="1" min="0" :value="$minSisa !== null ? (int) $minSisa : ''" placeholder="0" />
            </div>
            <div class="sm:col-span-12 flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 pt-4">
                <a href="{{ route('keuangan.tunggakan.index') }}" class="btn-nu">{{ __('Reset') }}</a>
                <x-primary-button type="submit">{{ __('Terapkan') }}</x-primary-button>
            </div>
        </form>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">{{ __('Siswa') }}</th>
                        <th class="px-5 py-3">{{ __('Kelas') }}</th>
                        <th class="px-5 py-3">{{ __('Jenis') }}</th>
                        <th class="px-5 py-3 font-mono">{{ __('Periode') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Tagihan') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Dibayar') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Sisa') }}</th>
                        <th class="px-5 py-3">{{ __('Status') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rows as $t)
                        @php
                            $bayar = (float) ($t->total_bayar ?? 0);
                            $sisa = max(0, (float) $t->jumlah - $bayar);
                            $parts = explode('-', $t->periode, 2);
                            $th = isset($parts[0]) ? (int) $parts[0] : (int) now()->year;
                            $bl = isset($parts[1]) ? (int) $parts[1] : (int) now()->month;
                        @endphp
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-5 py-3">
                                <div class="font-semibold text-gray-900">{{ $t->siswa?->nama ?? '—' }}</div>
                                <div class="text-xs font-mono text-gray-500">{{ $t->siswa?->nis ?? '' }}</div>
                            </td>
                            <td class="px-5 py-3 text-gray-700">
                                @if ($t->siswa?->kelas)
                                    {{ trim("{$t->siswa->kelas->tingkat} {$t->siswa->kelas->nama}") }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-800">{{ $t->jenis }}</td>
                            <td class="px-5 py-3 font-mono text-gray-800">{{ $t->periode }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900">Rp {{ number_format((int) round((float) $t->jumlah), 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right text-gray-700">Rp {{ number_format((int) round($bayar), 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-bold text-amber-800">Rp {{ number_format((int) round($sisa), 0, ',', '.') }}</td>
                            <td class="px-5 py-3">
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-900">{{ $t->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('keuangan.proses.index', ['siswa_id' => $t->siswa_id, 'bulan' => $bl, 'tahun' => $th]) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Bayar') }}</a>
                                <span class="mx-1 text-gray-300">|</span>
                                <a href="{{ route('tagihan.show', $t) }}" class="text-sm font-semibold text-gray-700 hover:underline">{{ __('Detail') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-8 text-center text-gray-600">{{ __('Tidak ada tunggakan untuk filter ini.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rows->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">{{ $rows->links() }}</div>
        @endif
    </div>
</x-app-layout>
