@php
    $jamDisplay = static fn ($v) => $v ? substr((string) $v, 0, 5) : '—';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Jadwal pelajaran') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Atur slot mapel per kelas, hari, dan jam.') }}</p>
            </div>
            @can('create', \App\Models\Jadwal::class)
                <a href="{{ route('jadwal.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    <span class="me-2 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    {{ __('Tambah jadwal') }}
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

        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
            <form method="GET" action="{{ route('jadwal.index') }}" class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                <div class="min-w-0 flex-1 sm:max-w-xs">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tahun ajaran') }}</label>
                    <select name="tahun_ajaran" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach ($tahunFilterOptions as $t)
                            <option value="{{ $t }}" {{ (string) $tahunAjaran === (string) $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0 flex-1 sm:max-w-xs">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kelas') }}</label>
                    <select name="kelas_id" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach ($filterKelasOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) $kelasId === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}{{ $k->is_active ? '' : ' (nonaktif)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Terapkan') }}
                    </button>
                    <a href="{{ route('jadwal.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar jadwal') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $jadwals->total() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Tahun') }}</th>
                            <th class="px-5 py-3">{{ __('Kelas') }}</th>
                            <th class="px-5 py-3">{{ __('Hari') }}</th>
                            <th class="px-5 py-3">{{ __('Jam') }}</th>
                            <th class="px-5 py-3">{{ __('Mapel') }}</th>
                            <th class="px-5 py-3">{{ __('Guru') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($jadwals as $j)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $j->tahun_ajaran }}</td>
                                <td class="px-5 py-3 text-gray-800">
                                    @if ($j->kelas)
                                        <span class="inline-flex items-center rounded-full bg-nu-primary/10 px-2.5 py-1 text-xs font-semibold text-nu-primary ring-1 ring-nu-primary/15">
                                            {{ $j->kelas->tingkat }} {{ $j->kelas->nama }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-800">{{ $j->hari }}</td>
                                <td class="px-5 py-3 font-mono text-gray-800">{{ $jamDisplay($j->jam_mulai) }}–{{ $jamDisplay($j->jam_selesai) }}</td>
                                <td class="px-5 py-3 text-gray-800">
                                    @if ($j->mataPelajaran)
                                        <span class="font-medium">{{ $j->mataPelajaran->nama }}</span>
                                        <span class="text-xs text-gray-500">({{ $j->mataPelajaran->kode }})</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-800">{{ $j->guru?->nama ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $j)
                                            <a href="{{ route('jadwal.edit', $j) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                                {{ __('Edit') }}
                                            </a>
                                        @endcan
                                        @can('delete', $j)
                                            <form method="POST" action="{{ route('jadwal.destroy', $j) }}" onsubmit="return confirm('{{ __('Hapus jadwal ini?') }}')">
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
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">
                                    {{ __('Belum ada jadwal. Sesuaikan filter atau tambah jadwal baru.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($jadwals->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $jadwals->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
