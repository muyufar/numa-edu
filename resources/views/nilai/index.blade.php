<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Nilai') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Rekap nilai akhir per siswa, mapel, dan semester.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('create', \App\Models\Nilai::class)
                    <a href="{{ route('nilai.bulk.create') }}" class="inline-flex items-center justify-center rounded-xl border border-nu-primary/30 bg-white px-4 py-2.5 text-sm font-semibold text-nu-primary shadow-sm hover:bg-nu-primary/5">
                        {{ __('Input massal') }}
                    </a>
                    <a href="{{ route('nilai.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Tambah nilai') }}
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
            <form method="GET" action="{{ route('nilai.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 sm:items-end">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kelas') }}</label>
                    <select name="kelas_id" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach ($filterKelasOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) $kelasId === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Mapel') }}</label>
                    <select name="mata_pelajaran_id" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach ($filterMapelOptions as $m)
                            <option value="{{ $m->id }}" {{ (string) $mapelId === (string) $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Semester') }}</label>
                    <select name="semester" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach (\App\Models\Nilai::SEMESTER_OPTIONS as $s)
                            <option value="{{ $s }}" {{ (string) $semester === (string) $s ? 'selected' : '' }}>
                                {{ $s === '1' ? __('Semester 1') : __('Semester 2') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tahun ajaran') }}</label>
                    <select name="tahun_ajaran" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach ($tahunFilterOptions as $t)
                            <option value="{{ $t }}" {{ (string) $tahunAjaran === (string) $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 lg:col-span-1">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Terapkan') }}
                    </button>
                    <a href="{{ route('nilai.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar nilai') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $nilais->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Tahun') }}</th>
                            <th class="px-5 py-3">{{ __('Smtr') }}</th>
                            <th class="px-5 py-3">{{ __('Kelas') }}</th>
                            <th class="px-5 py-3">{{ __('Siswa') }}</th>
                            <th class="px-5 py-3">{{ __('Mapel') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Nilai') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($nilais as $n)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-mono text-gray-900">{{ $n->tahun_ajaran }}</td>
                                <td class="px-5 py-3 text-gray-800">{{ $n->semester }}</td>
                                <td class="px-5 py-3 text-gray-800">
                                    @if ($n->kelas)
                                        {{ $n->kelas->tingkat }} {{ $n->kelas->nama }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $n->siswa?->nama ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-800">{{ $n->mataPelajaran?->nama ?? '—' }}</td>
                                <td class="px-5 py-3 text-right font-mono font-semibold text-gray-900">{{ $n->nilai_akhir !== null ? number_format((float) $n->nilai_akhir, 2, ',', '.') : '—' }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $n)
                                            <a href="{{ route('nilai.edit', $n) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                                {{ __('Edit') }}
                                            </a>
                                        @endcan
                                        @can('delete', $n)
                                            <form method="POST" action="{{ route('nilai.destroy', $n) }}" onsubmit="return confirm('{{ __('Hapus nilai ini?') }}')">
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
                                    {{ __('Belum ada data nilai.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($nilais->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $nilais->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
