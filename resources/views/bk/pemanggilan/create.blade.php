<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Jadwalkan pemanggilan') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Pilih kelas, lalu isi data pemanggilan siswa atau wali.') }}</p>
            </div>
            <a href="{{ route('bk.pemanggilan.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Riwayat') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <form method="GET" action="{{ route('bk.pemanggilan.create') }}" class="grid gap-4 sm:grid-cols-2 sm:items-end">
                <div>
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
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Tampilkan siswa') }}
                    </button>
                </div>
            </form>
        </div>

        @if ($kelasId && $siswas->isNotEmpty())
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <form method="POST" action="{{ route('bk.pemanggilan.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}" />
                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ __('Periksa kembali input yang kamu isi.') }}
                        </div>
                    @endif
                    @include('bk.pemanggilan._form', ['row' => null, 'siswas' => $siswas])
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                            {{ __('Simpan') }}
                        </button>
                    </div>
                </form>
            </div>
        @elseif ($kelasId && $siswas->isEmpty())
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ __('Tidak ada siswa di kelas ini. Tambahkan siswa di master terlebih dahulu.') }}
            </div>
        @endif
    </div>
</x-app-layout>
