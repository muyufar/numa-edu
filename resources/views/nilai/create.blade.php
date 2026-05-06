<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Tambah nilai') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Satu entri per kombinasi siswa, mapel, semester, dan tahun ajaran.') }}</p>
            </div>
            <a href="{{ route('nilai.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Langkah 1 — Pilih kelas') }}</h3>
            <p class="mt-1 text-sm text-gray-600">{{ __('Daftar siswa mengikuti kelas yang dipilih.') }}</p>
            <form method="GET" action="{{ route('nilai.create') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1 sm:max-w-md">
                    <label class="block text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
                    <select name="kelas_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                        <option value="">{{ __('— Pilih kelas —') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) old('kelas_id', $kelasId) === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}{{ $k->is_active ? '' : ' (nonaktif)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Lanjut') }}
                </button>
            </form>
        </div>

        @if ($kelasId && $siswaOptions->isNotEmpty())
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Langkah 2 — Isi nilai') }}</h3>
                <form method="POST" action="{{ route('nilai.store') }}" class="mt-6 space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ __('Periksa kembali input yang kamu isi.') }}
                        </div>
                    @endif

                    @include('nilai._form', [
                        'siswaOptions' => $siswaOptions,
                        'kelasOptions' => $kelasOptions,
                        'mapelOptions' => $mapelOptions,
                        'tahunAjaranOptions' => $tahunAjaranOptions,
                    ])

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                        <a href="{{ route('nilai.create') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                            {{ __('Ubah kelas') }}
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                            {{ __('Simpan') }}
                        </button>
                    </div>
                </form>
            </div>
        @elseif ($kelasId && $siswaOptions->isEmpty())
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ __('Tidak ada siswa di kelas ini.') }}
            </div>
        @endif
    </div>
</x-app-layout>
