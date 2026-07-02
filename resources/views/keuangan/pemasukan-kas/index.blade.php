<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Pemasukan kas') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Catat pemasukan non-siswa (hibah, bantuan, sewa, dll.); otomatis jurnal debit kas & kredit pendapatan.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('keuangan.index') }}" class="btn-nu">{{ __('Keuangan') }}</a>
                <a href="{{ route('keuangan.buku-kas.index') }}" class="btn-nu">{{ __('Buku kas') }}</a>
                <a href="{{ route('keuangan.pemasukan-kas.create') }}" class="btn-nu-primary">{{ __('Catat pemasukan') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <form method="GET" action="{{ route('keuangan.pemasukan-kas.index') }}" class="grid gap-4 sm:grid-cols-12 sm:items-end">
                <div class="sm:col-span-4">
                    <x-input-label for="tanggal_from" :value="__('Tanggal dari')" />
                    <x-text-input id="tanggal_from" name="tanggal_from" class="mt-2 block w-full" type="date" :value="request('tanggal_from')" />
                </div>
                <div class="sm:col-span-4">
                    <x-input-label for="tanggal_to" :value="__('Tanggal sampai')" />
                    <x-text-input id="tanggal_to" name="tanggal_to" class="mt-2 block w-full" type="date" :value="request('tanggal_to')" />
                </div>
                <div class="sm:col-span-12 flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 pt-4">
                    <a href="{{ route('keuangan.pemasukan-kas.index') }}" class="btn-nu">{{ __('Reset') }}</a>
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
                            <th class="px-5 py-3">{{ __('Keterangan') }}</th>
                            <th class="px-5 py-3">{{ __('Akun pendapatan') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Jumlah') }}</th>
                            <th class="px-5 py-3">{{ __('Bukti') }}</th>
                            <th class="px-5 py-3">{{ __('Oleh') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($items as $row)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-5 py-3 font-mono text-gray-800">{{ \App\Support\DateTimeFormat::date($row->tanggal) }}</td>
                                <td class="px-5 py-3 text-gray-800">{{ $row->keterangan }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $row->akunPendapatan?->kode }} {{ $row->akunPendapatan?->nama }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-emerald-800">Rp {{ number_format((float) $row->jumlah, 0, ',', '.') }}</td>
                                <td class="px-5 py-3">
                                    @if ($row->bukti_nota_path)
                                        <a href="{{ route('keuangan.pemasukan-kas.bukti-nota', $row) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Unduh') }}</a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $row->dibuatOleh?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-right">
                                    <form method="POST" action="{{ route('keuangan.pemasukan-kas.destroy', $row) }}" class="inline" onsubmit="return confirm(@json(__('Hapus pemasukan dan jurnal terkait?')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">{{ __('Hapus') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-gray-600">{{ __('Belum ada pemasukan.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($items->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $items->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
