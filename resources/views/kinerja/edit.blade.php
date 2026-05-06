<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Edit penilaian kinerja') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Perbarui data penilaian.') }}</p>
            </div>
            <a href="{{ route('kinerja.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <form method="POST" action="{{ route('kinerja.update', $item) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @include('kinerja._form', ['item' => $item])

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <a href="{{ route('kinerja.index') }}" class="btn-nu">{{ __('Batal') }}</a>
                        <button type="submit" class="btn-nu-primary">{{ __('Simpan perubahan') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

