<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-900">{{ __('Upload materi') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Unggah file materi untuk mapel/kelas tertentu.') }}</p>
            </div>
            <a href="{{ route('materi.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl">
        <div class="nu-surface overflow-hidden p-6 sm:p-8 ring-1 ring-black/5">
                <form method="POST" action="{{ route('materi.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @include('materi._form', ['materi_ajar' => null])
                    <div class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-end sm:gap-3">
                        <a href="{{ route('materi.index') }}" class="btn-nu w-full justify-center sm:w-auto">{{ __('Batal') }}</a>
                        <button class="btn-nu-primary w-full justify-center sm:w-auto" type="submit">{{ __('Upload') }}</button>
                    </div>
                </form>
        </div>
    </div>
</x-app-layout>

