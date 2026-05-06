<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="text-xl font-bold tracking-tight text-gray-900">{{ __('Pendaftaran PPDB') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('Isi data calon siswa. Tanpa login.') }}</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('ppdb.daftar.store') }}" class="space-y-4">
        @csrf

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
                {{ __('Periksa kembali isian.') }}
            </div>
        @endif

        @include('ppdb._form', ['showStatus' => false])

        <div class="flex flex-col gap-2 pt-2">
            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                {{ __('Kirim pendaftaran') }}
            </button>
            <a href="{{ url('/') }}" class="text-center text-sm font-semibold text-gray-600 hover:text-gray-900">{{ __('Kembali ke beranda') }}</a>
        </div>
    </form>
</x-guest-layout>
