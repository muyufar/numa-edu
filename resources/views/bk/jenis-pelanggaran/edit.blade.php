<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('BK — Edit jenis pelanggaran') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $row->nama }}</p>
            </div>
            <a href="{{ route('bk.jenis-pelanggaran.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
            <form method="POST" action="{{ route('bk.jenis-pelanggaran.update', $row) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('bk.jenis-pelanggaran._form', ['row' => $row])

                <div class="flex items-center justify-end gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Simpan perubahan') }}
                    </button>
                </div>
            </form>
        </div>

        @can('delete', $row)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Zona berbahaya') }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ __('Hapus jenis pelanggaran dari master data.') }}</p>
                <form method="POST" action="{{ route('bk.jenis-pelanggaran.destroy', $row) }}" class="mt-4" onsubmit="return confirm('{{ __('Hapus jenis pelanggaran ini?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                        {{ __('Hapus') }}
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-app-layout>
