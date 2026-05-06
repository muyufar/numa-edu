<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Inventaris · Edit Mutasi') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $mutasi->barang?->nama }}</p>
            </div>
            <a href="{{ route('inventaris.mutasi.index', ['barang_id' => $mutasi->inventaris_barang_id]) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
            <form method="POST" action="{{ route('inventaris.mutasi.update', $mutasi) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('inventaris.mutasi._form', ['mutasi' => $mutasi, 'barangOptions' => $barangOptions, 'tipeOptions' => $tipeOptions])

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-between">
                    @can('delete', $mutasi)
                        <form method="POST" action="{{ route('inventaris.mutasi.destroy', $mutasi) }}" onsubmit="return confirm('{{ __('Hapus mutasi ini?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                                {{ __('Hapus') }}
                            </button>
                        </form>
                    @else
                        <div></div>
                    @endcan

                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Simpan perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

