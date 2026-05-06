@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\KinerjaPenilaian[] $items */
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Kinerja Guru & Staff') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Catatan penilaian sederhana untuk monitoring dan tindak lanjut.') }}</p>
            </div>
            @can('create', \App\Models\KinerjaPenilaian::class)
                <a href="{{ route('kinerja.create') }}" class="btn-nu-primary">{{ __('Tambah penilaian') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">
                <div class="border-b border-gray-100 bg-gray-50 p-4">
                    <form method="GET" class="grid gap-3 md:grid-cols-12">
                        <div class="md:col-span-4">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari nama/aspek...') }}" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                        </div>
                        <div class="md:col-span-3">
                            <select name="target_type" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                <option value="">{{ __('Semua target') }}</option>
                                <option value="guru" @selected(request('target_type') === 'guru')>{{ __('Guru') }}</option>
                                <option value="pegawai" @selected(request('target_type') === 'pegawai')>{{ __('Pegawai') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <input type="text" name="periode" value="{{ request('periode') }}" placeholder="{{ __('Periode (YYYY-MM)') }}" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                        </div>
                        <div class="md:col-span-2 flex gap-2">
                            <button class="btn-nu-primary w-full" type="submit">{{ __('Filter') }}</button>
                            <a class="btn-nu w-full" href="{{ route('kinerja.index') }}">{{ __('Reset') }}</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-white">
                            <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-3">{{ __('Tanggal') }}</th>
                                <th class="px-5 py-3">{{ __('Target') }}</th>
                                <th class="px-5 py-3">{{ __('Periode') }}</th>
                                <th class="px-5 py-3">{{ __('Aspek') }}</th>
                                <th class="px-5 py-3">{{ __('Skor') }}</th>
                                <th class="px-5 py-3">{{ __('Dicatat oleh') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse ($items as $it)
                                <tr class="text-sm text-gray-700">
                                    <td class="px-5 py-4 whitespace-nowrap font-semibold text-gray-900">{{ optional($it->tanggal)->format('d M Y') }}</td>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $it->target_type === 'guru' ? ($it->guru->nama ?? '-') : ($it->pegawai->nama ?? '-') }}
                                        </div>
                                        <div class="mt-0.5 text-xs text-gray-500">{{ $it->target_type === 'guru' ? __('Guru') : __('Pegawai') }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">{{ $it->periode }}</td>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">{{ $it->aspek }}</div>
                                        @if ($it->catatan)
                                            <div class="mt-1 line-clamp-2 max-w-lg text-xs text-gray-500">{{ $it->catatan }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700 ring-1 ring-emerald-100">
                                            {{ (int) $it->skor }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">{{ $it->dibuatOleh->name ?? '-' }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                        @can('update', $it)
                                            <a href="{{ route('kinerja.edit', $it) }}" class="text-sm font-bold text-nu-primary hover:underline">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $it)
                                            <form action="{{ route('kinerja.destroy', $it) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="ms-3 text-sm font-bold text-red-600 hover:underline" onclick="return confirm('{{ __('Hapus penilaian ini?') }}')">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada data kinerja.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 bg-white p-4">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

