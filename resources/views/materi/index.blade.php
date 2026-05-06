@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\MateriAjar[] $items */
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Materi / Bahan Ajar') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Materi pelajaran dapat difilter per mapel/semester dan diunduh.') }}</p>
            </div>
            @can('create', \App\Models\MateriAjar::class)
                <a href="{{ route('materi.create') }}" class="btn-nu-primary">{{ __('Upload materi') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">
                <div class="border-b border-gray-100 bg-gray-50 p-4">
                    <form method="GET" class="grid gap-3 md:grid-cols-12">
                        <div class="md:col-span-4">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari judul/deskripsi/mapel...') }}" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                        </div>
                        <div class="md:col-span-4">
                            <select name="mata_pelajaran_id" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                <option value="">{{ __('Semua mapel') }}</option>
                                @foreach ($mapelOptions as $m)
                                    <option value="{{ $m->id }}" @selected((string) request('mata_pelajaran_id') === (string) $m->id)>
                                        {{ $m->kode ? $m->kode.' - ' : '' }}{{ $m->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <select name="semester" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                <option value="">{{ __('Semester') }}</option>
                                <option value="1" @selected(request('semester') === '1')>1</option>
                                <option value="2" @selected(request('semester') === '2')>2</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex gap-2">
                            <button class="btn-nu-primary w-full" type="submit">{{ __('Filter') }}</button>
                            <a class="btn-nu w-full" href="{{ route('materi.index') }}">{{ __('Reset') }}</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-white">
                            <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-3">{{ __('Judul') }}</th>
                                <th class="px-5 py-3">{{ __('Mapel') }}</th>
                                <th class="px-5 py-3">{{ __('Kelas') }}</th>
                                <th class="px-5 py-3">{{ __('Semester') }}</th>
                                <th class="px-5 py-3">{{ __('Ukuran') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse ($items as $it)
                                <tr class="text-sm text-gray-700">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">{{ $it->judul }}</div>
                                        <div class="mt-0.5 text-xs text-gray-500">
                                            {{ $it->tanggal ? $it->tanggal->format('d M Y') : '-' }}
                                            @if ($it->diunggahOleh) · {{ __('oleh') }} {{ $it->diunggahOleh->name }} @endif
                                        </div>
                                        @if ($it->deskripsi)
                                            <div class="mt-2 line-clamp-2 max-w-xl text-xs text-gray-500">{{ $it->deskripsi }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $it->mataPelajaran->nama ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $it->mataPelajaran->kode ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">
                                        {{ $it->kelas ? ($it->kelas->tingkat.' '.$it->kelas->nama) : __('Semua kelas') }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">{{ $it->semester ?? '-' }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">
                                        @php $kb = $it->size ? (int) round($it->size / 1024) : null; @endphp
                                        {{ $kb ? number_format($kb).' KB' : '-' }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                        @can('view', $it)
                                            <a href="{{ route('materi.download', $it) }}" class="text-sm font-bold text-nu-primary hover:underline">{{ __('Download') }}</a>
                                        @endcan
                                        @can('update', $it)
                                            <a href="{{ route('materi.edit', $it) }}" class="ms-3 text-sm font-bold text-gray-700 hover:underline">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $it)
                                            <form action="{{ route('materi.destroy', $it) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="ms-3 text-sm font-bold text-red-600 hover:underline" onclick="return confirm('{{ __('Hapus materi ini?') }}')">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada materi.') }}</td>
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

