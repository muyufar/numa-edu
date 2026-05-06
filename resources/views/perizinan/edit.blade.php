<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Edit perizinan') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Perbarui data atau status persetujuan.') }}</p>
            </div>
            <a href="{{ route('perizinan.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Daftar') }}</a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ __('Periksa kembali input yang kamu isi.') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <form method="GET" action="{{ route('perizinan.edit', $perizinan) }}" class="mb-6 grid gap-4 border-b border-gray-100 pb-6 sm:grid-cols-2 sm:items-end">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">{{ __('Ganti kelas (daftar siswa)') }}</label>
                    <select name="kelas_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Pilih kelas —') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) ($kelasId ?? '') === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Muat ulang') }}</button>
            </form>

            @if ($siswas->isEmpty())
                <p class="text-sm text-amber-800">{{ __('Tidak ada siswa untuk dipilih.') }}</p>
            @else
                <form method="POST" action="{{ route('perizinan.update', $perizinan) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}" />
                    @include('perizinan._form', ['perizinan' => $perizinan, 'siswas' => $siswas])
                    <div class="flex items-center justify-end border-t border-gray-100 pt-5">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Simpan') }}</button>
                    </div>
                </form>
            @endif
        </div>

        @can('delete', $perizinan)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Hapus pengajuan') }}</h3>
                <form method="POST" action="{{ route('perizinan.destroy', $perizinan) }}" class="mt-4" onsubmit="return confirm('{{ __('Hapus pengajuan ini?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">{{ __('Hapus') }}</button>
                </form>
            </div>
        @endcan
    </div>
</x-app-layout>
