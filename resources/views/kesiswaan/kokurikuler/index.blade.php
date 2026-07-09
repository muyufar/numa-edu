<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Kokurikuler') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kegiatan kokurikuler, laporan, dan LKPD.') }}</p>
            </div>
            @can('create', \App\Models\KokurikulerKegiatan::class)
                <a href="{{ route('kesiswaan.kokurikuler.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Catat kegiatan') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $rows->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Tanggal') }}</th>
                            <th class="px-5 py-3">{{ __('Judul') }}</th>
                            <th class="px-5 py-3 hidden md:table-cell">{{ __('Kelas') }}</th>
                            <th class="px-5 py-3 hidden lg:table-cell">{{ __('Tempat') }}</th>
                            <th class="px-5 py-3">{{ __('Peserta') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($rows as $row)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ $row->tanggal?->format('Y-m-d') }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $row->judul }}</td>
                                <td class="px-5 py-3 text-gray-600 hidden md:table-cell">
                                    @if ($row->kelas)
                                        {{ $row->kelas->tingkat }} {{ $row->kelas->nama }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-600 hidden lg:table-cell">{{ $row->tempat ?: '—' }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center rounded-lg bg-nu-primary/10 px-2.5 py-1 text-xs font-semibold text-nu-primary">
                                        {{ $row->anggotas_count }} {{ __('siswa') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    @if ($row->status === 'publish')
                                        <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">{{ __('Publish') }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ __('Draft') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @can('update', $row)
                                        <a href="{{ route('kesiswaan.kokurikuler.edit', $row) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada data.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($rows->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $rows->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
