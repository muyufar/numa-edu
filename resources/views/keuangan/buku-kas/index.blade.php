<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Buku kas') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Mutasi akun Kas menurut jurnal; saldo berjalan dalam rentang tanggal.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('keuangan.index') }}" class="btn-nu">{{ __('Keuangan') }}</a>
                <a href="{{ route('keuangan.pengeluaran-kas.index') }}" class="btn-nu">{{ __('Pengeluaran kas') }}</a>
                <a href="{{ route('keuangan.buku-kas.export', request()->query()) }}" class="btn-nu-primary">{{ __('Unduh CSV') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <form method="GET" action="{{ route('keuangan.buku-kas.index') }}" class="grid gap-4 sm:grid-cols-12 sm:items-end">
                <div class="sm:col-span-4">
                    <x-input-label for="tanggal_from" :value="__('Tanggal dari')" />
                    <x-text-input id="tanggal_from" name="tanggal_from" class="mt-2 block w-full" type="date" :value="$tanggalFrom" />
                </div>
                <div class="sm:col-span-4">
                    <x-input-label for="tanggal_to" :value="__('Tanggal sampai')" />
                    <x-text-input id="tanggal_to" name="tanggal_to" class="mt-2 block w-full" type="date" :value="$tanggalTo" />
                </div>
                <div class="sm:col-span-12 flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 pt-4">
                    <a href="{{ route('keuangan.buku-kas.index') }}" class="btn-nu">{{ __('Bulan ini') }}</a>
                    <x-primary-button type="submit">{{ __('Terapkan') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Akun') }}</div>
                <div class="mt-1 font-mono text-sm font-bold text-gray-900">{{ $kas->kode }} {{ $kas->nama }}</div>
            </div>
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Saldo awal periode') }}</div>
                <div class="mt-1 text-lg font-extrabold text-gray-900">Rp {{ number_format((int) round($saldoAwal), 0, ',', '.') }}</div>
            </div>
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Saldo akhir periode') }}</div>
                <div class="mt-1 text-lg font-extrabold text-nu-primary">Rp {{ number_format((int) round($saldoAkhir), 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Tanggal') }}</th>
                            <th class="px-5 py-3">{{ __('No. bukti') }}</th>
                            <th class="px-5 py-3">{{ __('Keterangan') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Debit') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Kredit') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Saldo') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($lines as $line)
                            @php($j = $line->jurnal)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-5 py-3 font-mono text-gray-800">{{ $j ? \App\Support\DateTimeFormat::date($j->tanggal) : '—' }}</td>
                                <td class="px-5 py-3 font-mono text-gray-600">{{ $j?->no_bukti ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-800">{{ $j?->keterangan ?? '—' }}</td>
                                <td class="px-5 py-3 text-right font-mono">{{ number_format((float) $line->debit, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right font-mono">{{ number_format((float) $line->kredit, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-900">Rp {{ number_format((int) round($line->saldo_setelah), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-600">{{ __('Tidak ada mutasi kas pada rentang ini.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
