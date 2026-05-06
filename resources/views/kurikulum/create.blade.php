<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Tambah item kurikulum') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Tentukan mapel untuk tingkat dan semester tertentu.') }}</p>
            </div>
            <a href="{{ route('kurikulum.index', request()->only(['tahun_ajaran', 'semester'])) }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
        <form method="POST" action="{{ route('kurikulum.store') }}" class="space-y-6">
            @csrf
            @include('kurikulum._form', ['item' => null])
            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('kurikulum.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Simpan') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
