<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Edit pegawai') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $pegawai->nama }}</p>
            </div>
            <a href="{{ route('pegawai.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <form method="POST" action="{{ route('pegawai.update', $pegawai) }}" class="space-y-6">
                @csrf
                @method('PUT')
                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ __('Periksa kembali input yang kamu isi.') }}</div>
                @endif
                @include('pegawai._form', ['pegawai' => $pegawai])
                <div class="flex items-center justify-end border-t border-gray-100 pt-5">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Simpan perubahan') }}</button>
                </div>
            </form>
        </div>

        @can('delete', $pegawai)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Hapus pegawai') }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ __('Menghapus juga riwayat presensi terkait.') }}</p>
                <form method="POST" action="{{ route('pegawai.destroy', $pegawai) }}" class="mt-4" onsubmit="return confirm('{{ __('Hapus pegawai ini?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">{{ __('Hapus') }}</button>
                </form>
            </div>
        @endcan
    </div>
</x-app-layout>
