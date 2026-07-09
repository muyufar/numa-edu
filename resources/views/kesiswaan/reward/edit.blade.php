<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Edit reward') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Perbarui catatan reward siswa.') }}</p>
            </div>
            <a href="{{ route('kesiswaan.reward.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Riwayat') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ __('Periksa kembali input yang kamu isi.') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            @if ($siswas->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {{ __('Tidak dapat memuat daftar siswa untuk baris ini.') }}
                </div>
            @else
                <form method="POST" action="{{ route('kesiswaan.reward.update', $row) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('kesiswaan.reward._form', ['row' => $row, 'siswas' => $siswas])
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                            {{ __('Simpan perubahan') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>

        @can('delete', $row)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Zona berbahaya') }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ __('Hapus catatan reward permanen dari basis data.') }}</p>
                <form method="POST" action="{{ route('kesiswaan.reward.destroy', $row) }}" class="mt-4" onsubmit="return confirm('{{ __('Hapus catatan ini?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                        {{ __('Hapus catatan') }}
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-app-layout>
