<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">{{ __('Tambah buku') }}</h2>
            <a href="{{ route('perpustakaan.buku.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <form method="POST" action="{{ route('perpustakaan.buku.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('perpustakaan.buku._form', ['buku' => $buku, 'kategoriOptions' => $kategoriOptions])
            <button class="btn-nu-primary" type="submit">{{ __('Simpan') }}</button>
        </form>
    </div>
</x-app-layout>
