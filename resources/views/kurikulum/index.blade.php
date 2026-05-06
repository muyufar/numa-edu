@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\KurikulumItem[] $items */
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Kurikulum') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Struktur mapel per tingkat, semester, dan tahun ajaran.') }}</p>
            </div>
            @can('create', \App\Models\KurikulumItem::class)
                <a href="{{ route('kurikulum.create', request()->only(['tahun_ajaran', 'semester'])) }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Tambah item') }}
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

        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
            <form method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-500">{{ __('Tahun ajaran') }}</label>
                    <input type="text" name="tahun_ajaran" value="{{ request('tahun_ajaran') }}" list="ta-list" class="mt-1 w-full rounded-xl border-gray-200 bg-white shadow-sm" placeholder="2025/2026">
                    <datalist id="ta-list">
                        @foreach ($tahunAjaranOptions as $ta)
                            <option value="{{ $ta }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">{{ __('Semester') }}</label>
                    <select name="semester" class="mt-1 w-full rounded-xl border-gray-200 bg-white shadow-sm">
                        <option value="">{{ __('Semua') }}</option>
                        <option value="1" @selected(request('semester') === '1')>1</option>
                        <option value="2" @selected(request('semester') === '2')>2</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">{{ __('Tingkat') }}</label>
                    <input type="number" name="tingkat" min="1" max="12" value="{{ request('tingkat') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white shadow-sm" placeholder="1–12">
                </div>
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-500">{{ __('Mapel') }}</label>
                    <select name="mata_pelajaran_id" class="mt-1 w-full rounded-xl border-gray-200 bg-white shadow-sm">
                        <option value="">{{ __('Semua') }}</option>
                        @foreach ($mapelOptions as $m)
                            <option value="{{ $m->id }}" @selected((string) request('mata_pelajaran_id') === (string) $m->id)>
                                {{ $m->kode ? $m->kode.' - ' : '' }}{{ $m->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-6">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Filter') }}</button>
                    <a href="{{ route('kurikulum.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar item kurikulum') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $items->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Mapel') }}</th>
                            <th class="px-5 py-3">{{ __('Tingkat') }}</th>
                            <th class="px-5 py-3">{{ __('Sem.') }}</th>
                            <th class="px-5 py-3">{{ __('TA') }}</th>
                            <th class="px-5 py-3">{{ __('Jam/mgg') }}</th>
                            <th class="px-5 py-3">{{ __('Urut') }}</th>
                            <th class="px-5 py-3">{{ __('Aktif') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($items as $it)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-semibold text-gray-900">{{ $it->mataPelajaran->nama ?? '-' }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $it->tingkat }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $it->semester }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $it->tahun_ajaran }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $it->jam_per_minggu ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $it->urutan }}</td>
                                <td class="px-5 py-3">
                                    @if ($it->is_active)
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-800">{{ __('Ya') }}</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">{{ __('Tidak') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $it)
                                            <a href="{{ route('kurikulum.edit', $it) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $it)
                                            <form method="POST" action="{{ route('kurikulum.destroy', $it) }}" onsubmit="return confirm('{{ __('Hapus item ini?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">{{ __('Hapus') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada data kurikulum.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
