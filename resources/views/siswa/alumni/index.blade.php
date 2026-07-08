<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Daftar alumni') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Siswa dengan status Alumni, Lulus, atau Tamat.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('siswa.index') }}" class="btn-nu">{{ __('Kembali ke siswa') }}</a>
                @can('create', \App\Models\Siswa::class)
                    <a href="{{ route('siswa.kenaikan-kelas.index') }}" class="btn-nu-primary">{{ __('Kenaikan kelas') }}</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600">{{ __('Cari') }}</label>
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="{{ __('Nama, NIS, atau NISN') }}"
                    class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
                />
            </div>
            <button type="submit" class="btn-nu-primary">{{ __('Filter') }}</button>
        </form>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Alumni terdaftar') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $alumnis->total() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('NIS') }}</th>
                            <th class="px-5 py-3">{{ __('NISN') }}</th>
                            <th class="px-5 py-3">{{ __('Nama') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3">{{ __('Kelas terakhir') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($alumnis as $a)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-mono font-semibold text-gray-900">{{ $a->nis }}</td>
                                <td class="px-5 py-3 font-mono text-gray-700">{{ $a->nisn ?: '—' }}</td>
                                <td class="px-5 py-3 text-gray-900">{{ $a->nama }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-100">
                                        {{ $a->status ?: __('Alumni') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-700">
                                    @if ($a->kelas)
                                        {{ $a->kelas->tingkat }} {{ $a->kelas->nama }}
                                        <span class="text-xs text-gray-400">· {{ $a->kelas->tahun_ajaran }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">{{ $a->tingkat_rombel ?: __('—') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @can('update', $a)
                                        <a href="{{ route('siswa.edit', $a) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">
                                    {{ __('Belum ada alumni. Set status siswa menjadi Alumni, Lulus, atau Tamat.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($alumnis->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $alumnis->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
