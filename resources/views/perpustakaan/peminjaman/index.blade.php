<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">{{ $isPetugas ? __('Manajemen peminjaman') : __('Peminjaman saya') }}</h2>
    </x-slot>

    @if ($isPetugas)
        <div class="mb-4 flex flex-wrap gap-2">
            <a href="{{ route('perpustakaan.peminjaman.index', ['tab' => 'semua']) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 {{ $tab === 'semua' ? 'bg-nu-primary text-white' : 'bg-white text-gray-700' }}">{{ __('Semua') }}</a>
            <a href="{{ route('perpustakaan.peminjaman.index', ['tab' => 'aktif']) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 {{ $tab === 'aktif' ? 'bg-nu-primary text-white' : 'bg-white text-gray-700' }}">{{ __('Aktif') }}</a>
            <a href="{{ route('perpustakaan.peminjaman.index', ['tab' => 'terlambat']) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 {{ $tab === 'terlambat' ? 'bg-nu-primary text-white' : 'bg-white text-gray-700' }}">{{ __('Terlambat') }}</a>
        </div>
    @endif

    <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Buku') }}</th>
                    <th class="px-4 py-3">{{ __('Peminjam') }}</th>
                    <th class="px-4 py-3">{{ __('Tipe') }}</th>
                    <th class="px-4 py-3">{{ __('Jatuh tempo') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($peminjamans as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><a href="{{ route('perpustakaan.peminjaman.show', $p) }}" class="font-semibold text-nu-primary hover:underline">{{ $p->buku?->judul }}</a></td>
                        <td class="px-4 py-3">{{ $p->namaPeminjam() }}</td>
                        <td class="px-4 py-3">{{ ucfirst($p->tipe_peminjaman) }}</td>
                        <td class="px-4 py-3">{{ $p->tanggal_jatuh_tempo->format('d M Y') }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-semibold ring-1 {{ $p->badgeStatusClass() }}">{{ $p->labelStatus() }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('Tidak ada data.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $peminjamans->links() }}</div>
    </div>
</x-app-layout>
