<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Inventaris · Edit Barang') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $barang->nama }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('inventaris.mutasi.index', ['barang_id' => $barang->id]) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    {{ __('Lihat mutasi') }}
                </a>
                <a href="{{ route('inventaris.barang.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    {{ __('Kembali') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
            <div class="mb-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Stok akhir') }}</div>
                    <div class="mt-1 font-mono text-lg font-bold text-gray-900">{{ $barang->stok_akhir }}</div>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Stok awal') }}</div>
                    <div class="mt-1 font-mono text-lg font-bold text-gray-900">{{ $barang->stok_awal }}</div>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Minimum') }}</div>
                    <div class="mt-1 font-mono text-lg font-bold text-gray-900">{{ $barang->stok_minimum }}</div>
                </div>
            </div>

            <form id="inventaris-barang-form" method="POST" action="{{ route('inventaris.barang.update', $barang) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                @include('inventaris.barang._form', ['barang' => $barang, 'kategoriOptions' => $kategoriOptions])
            </form>

            <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-between">
                @can('delete', $barang)
                    <form method="POST" action="{{ route('inventaris.barang.destroy', $barang) }}" onsubmit="return confirm('{{ __('Hapus barang ini?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                            {{ __('Hapus') }}
                        </button>
                    </form>
                @else
                    <div></div>
                @endcan

                <button type="submit" form="inventaris-barang-form" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Simpan perubahan') }}
                </button>
            </div>
        </div>
    </div>
</x-app-layout>

