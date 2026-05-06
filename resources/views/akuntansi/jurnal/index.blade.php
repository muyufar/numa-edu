<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Daftar jurnal') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Semua jurnal (manual, pembayaran, pengeluaran) dalam rentang tanggal.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('akuntansi.index') }}" class="btn-nu">{{ __('Ringkasan') }}</a>
                <a href="{{ route('akuntansi.jurnal.export', request()->query()) }}" class="btn-nu">{{ __('Unduh CSV') }}</a>
                @can('create', \App\Models\Tagihan::class)
                    <a href="{{ route('akuntansi.jurnal.create') }}" class="btn-nu-primary">{{ __('Jurnal manual') }}</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->has('jurnal'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                {{ $errors->first('jurnal') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <form method="GET" action="{{ route('akuntansi.jurnal.index') }}" class="grid gap-4 sm:grid-cols-12 sm:items-end">
                <div class="sm:col-span-3">
                    <x-input-label for="tanggal_from" :value="__('Tanggal dari')" />
                    <x-text-input id="tanggal_from" name="tanggal_from" class="mt-2 block w-full" type="date" :value="$tanggalFrom" />
                </div>
                <div class="sm:col-span-3">
                    <x-input-label for="tanggal_to" :value="__('Tanggal sampai')" />
                    <x-text-input id="tanggal_to" name="tanggal_to" class="mt-2 block w-full" type="date" :value="$tanggalTo" />
                </div>
                <div class="sm:col-span-4">
                    <x-input-label for="q" :value="__('Cari keterangan')" />
                    <x-text-input id="q" name="q" class="mt-2 block w-full" type="search" :value="$q" />
                </div>
                <div class="sm:col-span-12 flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 pt-4">
                    <x-primary-button type="submit">{{ __('Terapkan') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Tanggal') }}</th>
                            <th class="px-5 py-3">{{ __('No. bukti') }}</th>
                            <th class="px-5 py-3">{{ __('Keterangan') }}</th>
                            <th class="px-5 py-3">{{ __('Sumber') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Baris') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Total debit') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Detail / hapus') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($rows as $row)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-5 py-3 font-mono text-gray-800">{{ \App\Support\DateTimeFormat::date($row->tanggal) }}</td>
                                <td class="px-5 py-3 font-mono text-gray-600">{{ $row->no_bukti ?? '—' }}</td>
                                <td class="max-w-xs truncate px-5 py-3 text-gray-800">{{ $row->keterangan ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ \App\Support\ManualJurnalService::sumberLabel($row->sumber_type) }}</td>
                                <td class="px-5 py-3 text-right font-mono">{{ number_format($row->lines_count) }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-900">Rp {{ number_format((int) round((float) ($row->lines_sum_debit ?? 0)), 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('akuntansi.jurnal.show', $row) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Detail') }}</a>
                                    @if ($row->sumber_type === null && $row->sumber_id === null)
                                        @can('create', \App\Models\Tagihan::class)
                                            <span class="mx-1 text-gray-300">|</span>
                                            <form method="POST" action="{{ route('akuntansi.jurnal.destroy', $row) }}" class="inline" onsubmit="return confirm(@json(__('Hapus jurnal manual ini?')))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">{{ __('Hapus') }}</button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-gray-600">{{ __('Tidak ada jurnal pada rentang ini.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($rows->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $rows->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
