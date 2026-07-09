<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold">{{ __('Edit kategori') }}</h2></x-slot>
    <div class="mx-auto max-w-xl rounded-3xl bg-white p-6 ring-1 ring-black/5">
        <form method="POST" action="{{ route('perpustakaan.kategori.update', $kategori) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('perpustakaan.kategori._form', ['kategori' => $kategori])
            <button class="btn-nu-primary" type="submit">{{ __('Simpan') }}</button>
        </form>
    </div>
</x-app-layout>
