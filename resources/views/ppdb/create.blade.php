<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Tambah pendaftaran PPDB') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Entri manual oleh staf (mis. walk-in).') }}</p>
            </div>
            <a href="{{ route('ppdb.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
        <form method="POST" action="{{ route('ppdb.store') }}" class="space-y-6">
            @csrf
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ __('Periksa kembali isian.') }}</div>
            @endif
            @include('ppdb._form', ['showStatus' => false])
            <div class="flex justify-end border-t border-gray-100 pt-5">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Simpan') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
