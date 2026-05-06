<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Inventaris · Tambah Kategori') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Buat kategori baru untuk pengelompokan barang.') }}</p>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
            <form method="POST" action="{{ route('inventaris.kategori.store') }}" class="space-y-5">
                @csrf

                @include('inventaris.kategori._form', ['kategori' => $kategori])

                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('inventaris.kategori.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Simpan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

