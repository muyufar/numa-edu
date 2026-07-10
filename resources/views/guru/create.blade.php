<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tambah Data GTK') }}</p>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Tambah guru') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Buat akun guru dengan data profil lengkap.') }}</p>
            </div>
            <a href="{{ route('tenaga-kependidikan.index', ['tab' => 'guru']) }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
        <form method="POST" action="{{ route('guru.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ __('Periksa kembali input yang kamu isi.') }}
                </div>
            @endif

            @include('tenaga-kependidikan._form', ['entity' => $guru, 'type' => 'guru'])

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('tenaga-kependidikan.index', ['tab' => 'guru']) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                    {{ __('Batal') }}
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    {{ __('Simpan') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
