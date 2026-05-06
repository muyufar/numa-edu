<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Inventaris · Barang') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kelola master barang dan cek stok akhir.') }}</p>
            </div>
            @can('create', \App\Models\InventarisBarang::class)
                <a href="{{ route('inventaris.barang.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Tambah') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
            <form method="GET" action="{{ route('inventaris.barang.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 sm:items-end">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Cari') }}</label>
                    <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('Nama / kode') }}" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kategori') }}</label>
                    <select name="kategori_id" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach ($kategoriOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) $kategoriId === (string) $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</label>
                    <select name="active" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="1" {{ $active === '1' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                        <option value="0" {{ $active === '0' ? 'selected' : '' }}>{{ __('Nonaktif') }}</option>
                        <option value="all" {{ $active === 'all' ? 'selected' : '' }}>{{ __('Semua') }}</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Terapkan') }}</button>
                    <a href="{{ route('inventaris.barang.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $barangs->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Barang') }}</th>
                            <th class="px-5 py-3">{{ __('Kategori') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Stok akhir') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($barangs as $b)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $b->nama }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $b->kode ?: '—' }} · {{ $b->satuan }}</div>
                                </td>
                                <td class="px-5 py-3 text-gray-700">{{ $b->kategori?->nama ?: '—' }}</td>
                                <td class="px-5 py-3 text-right font-mono text-xs text-gray-700">{{ $b->stok_akhir }}</td>
                                <td class="px-5 py-3 text-right space-x-2">
                                    <a href="{{ route('inventaris.mutasi.index', ['barang_id' => $b->id]) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Mutasi') }}</a>
                                    @can('update', $b)
                                        <a href="{{ route('inventaris.barang.edit', $b) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada data.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($barangs->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $barangs->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

