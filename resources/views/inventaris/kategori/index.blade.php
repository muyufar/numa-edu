<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Inventaris · Kategori') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kelola kategori barang inventaris.') }}</p>
            </div>
            @can('create', \App\Models\InventarisKategori::class)
                <a href="{{ route('inventaris.kategori.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Tambah') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $kategoris->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Nama') }}</th>
                            <th class="px-5 py-3">{{ __('Deskripsi') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($kategoris as $k)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $k->nama }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit((string) $k->deskripsi, 70) }}</td>
                                <td class="px-5 py-3 text-right">
                                    @can('update', $k)
                                        <a href="{{ route('inventaris.kategori.edit', $k) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada data.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($kategoris->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $kategoris->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

