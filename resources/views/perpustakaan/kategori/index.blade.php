<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">{{ __('Kategori buku') }}</h2>
            <a href="{{ route('perpustakaan.kategori.create') }}" class="btn-nu-primary">{{ __('Tambah') }}</a>
        </div>
    </x-slot>
    <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">{{ __('Nama') }}</th><th class="px-4 py-3">{{ __('Kode') }}</th><th class="px-4 py-3">{{ __('Buku') }}</th><th class="px-4 py-3"></th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($kategoris as $k)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $k->nama }}</td>
                        <td class="px-4 py-3">{{ $k->kode ?: '—' }}</td>
                        <td class="px-4 py-3">{{ $k->bukus_count }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('perpustakaan.kategori.edit', $k) }}" class="text-nu-primary hover:underline">{{ __('Edit') }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $kategoris->links() }}</div>
    </div>
</x-app-layout>
