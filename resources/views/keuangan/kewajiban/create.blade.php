<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Tambah kewajiban pembayaran') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Buat master kewajiban sebagai template pembuatan tagihan.') }}</p>
            </div>
            <a href="{{ route('keuangan.kewajiban.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
        <form method="POST" action="{{ route('keuangan.kewajiban.store') }}" class="space-y-6">
            @csrf
            @include('keuangan.kewajiban.form')

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('keuangan.kewajiban.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                    {{ __('Batal') }}
                </a>
                <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>

