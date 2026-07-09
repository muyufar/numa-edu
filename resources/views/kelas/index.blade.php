<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Master Kelas') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kelola data kelas/rombel per tahun ajaran.') }}</p>
            </div>
            @can('create', \App\Models\Kelas::class)
                <a href="{{ route('kelas.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    <span class="me-2 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    {{ __('Tambah kelas') }}
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
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar kelas') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $kelas->total() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Tingkat') }}</th>
                            <th class="px-5 py-3">{{ __('Nama') }}</th>
                            <th class="px-5 py-3">{{ __('Tahun ajaran') }}</th>
                            <th class="px-5 py-3">{{ __('Wali kelas') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($kelas as $k)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-semibold text-gray-900">{{ $k->tingkat }}</td>
                                <td class="px-5 py-3 text-gray-900">{{ $k->nama }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $k->tahun_ajaran }}</td>
                                <td class="px-5 py-3 text-gray-700">
                                    @if ($k->waliKelas)
                                        {{ $k->waliKelas->nama }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if($k->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-gray-200">{{ __('Nonaktif') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $k)
                                            <a href="{{ route('kelas.edit', $k) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                                {{ __('Edit') }}
                                            </a>
                                        @endcan
                                        @can('delete', $k)
                                            <form method="POST" action="{{ route('kelas.destroy', $k) }}" onsubmit="return confirm('{{ __('Hapus kelas ini?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">
                                    {{ __('Belum ada data kelas.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($kelas->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $kelas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

