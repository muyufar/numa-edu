<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Tambah penilaian kinerja') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Isi penilaian sederhana untuk guru atau pegawai.') }}</p>
            </div>
            <a href="{{ route('kinerja.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <form method="POST" action="{{ route('kinerja.store') }}" class="space-y-6">
                    @csrf

                    @include('kinerja._form', ['item' => null])

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <a href="{{ route('kinerja.index') }}" class="btn-nu">{{ __('Batal') }}</a>
                        <button type="submit" class="btn-nu-primary">{{ __('Simpan') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

