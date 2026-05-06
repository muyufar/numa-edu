<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Berita & informasi') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kelola konten untuk halaman publik Informasi.') }}</p>
            </div>
            @can('create', \App\Models\Berita::class)
                <a href="{{ route('berita.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Tulis berita') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar') }}</div>
                <a href="{{ route('informasi.index') }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-nu-primary hover:underline">{{ __('Lihat halaman publik') }} →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Judul') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3 hidden sm:table-cell">{{ __('Penulis') }}</th>
                            <th class="px-5 py-3 hidden md:table-cell">{{ __('Diperbarui') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($beritas as $row)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $row->judul }}</td>
                                <td class="px-5 py-3">
                                    @if ($row->is_published)
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200/80">{{ __('Terbit') }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('Draf') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-600 hidden sm:table-cell">{{ $row->author?->name ?? '—' }}</td>
                                <td class="px-5 py-3 font-mono text-xs text-gray-500 hidden md:table-cell">{{ $row->updated_at?->format('Y-m-d') }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        @if ($row->is_published)
                                            <a href="{{ route('informasi.show', $row->slug) }}" target="_blank" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Lihat') }}</a>
                                        @endif
                                        @can('update', $row)
                                            <a href="{{ route('berita.edit', $row) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Edit') }}</a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada berita.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($beritas->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $beritas->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
