@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Tugas[] $items */
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Tugas') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kelola penugasan per mapel, kelas, hari, dan batas kumpul.') }}</p>
            </div>
            @can('create', \App\Models\Tugas::class)
                <a href="{{ route('tugas.create') }}" class="btn-nu-primary">{{ __('Buat tugas') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">
                <div class="border-b border-gray-100 bg-gray-50 p-4">
                    <form method="GET" class="grid gap-3 md:grid-cols-12">
                        <div class="md:col-span-3">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari judul/materi/mapel...') }}" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                        </div>
                        <div class="md:col-span-2">
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
                            <select name="kelas_id" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                <option value="">{{ __('Semua kelas') }}</option>
                                @foreach ($kelasOptions as $k)
                                    <option value="{{ $k->id }}" @selected((string) request('kelas_id') === (string) $k->id)>
                                        {{ $k->tingkat }} {{ $k->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <select name="hari" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                <option value="">{{ __('Semua hari') }}</option>
                                @foreach (\App\Models\Tugas::HARI_OPTIONS as $h)
                                    <option value="{{ $h }}" @selected(request('hari') === $h)>{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <select name="status" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                <option value="">{{ __('Status') }}</option>
                                <option value="aktif" @selected(request('status') === 'aktif')>{{ __('Aktif') }}</option>
                                <option value="lewat" @selected(request('status') === 'lewat')>{{ __('Lewat batas') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex gap-2">
                            <button class="btn-nu-primary w-full" type="submit">{{ __('Filter') }}</button>
                            <a class="btn-nu w-full" href="{{ route('tugas.index') }}">{{ __('Reset') }}</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-white">
                            <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-3">{{ __('Judul') }}</th>
                                <th class="px-5 py-3">{{ __('Mapel / Kelas') }}</th>
                                <th class="px-5 py-3">{{ __('Hari · Jam') }}</th>
                                <th class="px-5 py-3">{{ __('Batas kumpul') }}</th>
                                <th class="px-5 py-3">{{ __('Tipe') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse ($items as $it)
                                <tr class="text-sm text-gray-700">
                                    <td class="px-5 py-4">
                                        <div class="flex items-start gap-2">
                                            <div class="min-w-0 flex-1">
                                                <div class="font-semibold text-gray-900">{{ $it->judul }}</div>
                                                @if ($it->bobot)
                                                    <div class="mt-0.5 text-xs text-gray-500">{{ __('Bobot') }}: {{ $it->bobot }} {{ __('poin') }}</div>
                                                @endif
                                                <div class="mt-0.5 text-xs text-indigo-600">{{ \App\Models\Tugas::jenisSoalLabel($it->jenis_soal) }}</div>
                                                @if (! $it->is_published)
                                                    <span class="mt-1 inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">{{ __('Draft') }}</span>
                                                @endif
                                            </div>
                                            @if ($it->isOverdue())
                                                <span class="shrink-0 rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-red-200">{{ __('Lewat') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $it->mataPelajaran->nama ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $it->kelas ? ($it->kelas->tingkat.' '.$it->kelas->nama) : __('Semua kelas') }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">{{ $it->jadwalLabel() }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">{{ $it->batasLabel() }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">{{ \App\Models\Tugas::tipeLabel($it->tipe) }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                        <div class="flex flex-col items-end gap-1">
                                            @can('submit', $it)
                                                <a href="{{ route('tugas.kerjakan', $it) }}" class="inline-flex items-center rounded-lg bg-nu-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-nu-primary-light">
                                                    {{ __('Kerjakan tugas') }}
                                                </a>
                                            @elseif (isset($pengumpulanByTugasId[$it->id]))
                                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">{{ __('Sudah dikumpulkan') }}</span>
                                            @elseif (auth()->user()?->hasRole('siswa') && $it->isOverdue())
                                                <span class="text-xs font-semibold text-red-600">{{ __('Lewat batas') }}</span>
                                            @endcan
                                            @can('view', $it)
                                                <a href="{{ route('tugas.show', $it) }}" class="text-sm font-bold text-nu-primary hover:underline">{{ __('Detail') }}</a>
                                            @endcan
                                        @can('update', $it)
                                            <a href="{{ route('tugas.edit', $it) }}" class="ms-3 text-sm font-bold text-gray-700 hover:underline">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $it)
                                            <form action="{{ route('tugas.destroy', $it) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="ms-3 text-sm font-bold text-red-600 hover:underline" onclick="return confirm('{{ __('Hapus tugas ini?') }}')">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada tugas.') }}</td>
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
