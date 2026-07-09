<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Reward siswa') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Poin positif prestasi dan administrasi per siswa.') }}</p>
            </div>
            @can('create', \App\Models\RewardSiswa::class)
                <a href="{{ route('kesiswaan.reward.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Catat reward') }}
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
            <form method="GET" action="{{ route('kesiswaan.reward.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 sm:items-end">
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
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Siswa') }}</label>
                    <select name="siswa_id" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" @disabled($siswaFilterOptions->isEmpty())>
                        <option value="">{{ __('— Semua di kelas —') }}</option>
                        @foreach ($siswaFilterOptions as $s)
                            <option value="{{ $s->id }}" {{ (string) $siswaId === (string) $s->id ? 'selected' : '' }}>
                                {{ $s->nis }} — {{ $s->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Terapkan') }}
                    </button>
                    <a href="{{ route('kesiswaan.reward.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

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
                            <th class="px-5 py-3">{{ __('Siswa') }}</th>
                            <th class="px-5 py-3">{{ __('Kategori') }}</th>
                            <th class="px-5 py-3">{{ __('Judul') }}</th>
                            <th class="px-5 py-3 hidden md:table-cell">{{ __('Poin') }}</th>
                            <th class="px-5 py-3 hidden lg:table-cell">{{ __('Keterangan') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($rows as $row)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ $row->tanggal?->format('Y-m-d') }}</td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $row->siswa?->nama }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $row->siswa?->nis }}@if ($row->siswa?->kelas) · {{ $row->siswa->kelas->tingkat }} {{ $row->siswa->kelas->nama }}@endif</div>
                                </td>
                                <td class="px-5 py-3 text-gray-800">{{ \App\Models\RewardSiswa::kategoriLabel($row->kategori) }}</td>
                                <td class="px-5 py-3 text-gray-800">{{ $row->judul }}</td>
                                <td class="px-5 py-3 text-gray-600 hidden md:table-cell">
                                    <span class="font-mono text-xs">{{ $row->poin }} {{ __('poin') }}</span>
                                </td>
                                <td class="px-5 py-3 text-gray-600 hidden lg:table-cell max-w-xs truncate" title="{{ $row->keterangan }}">{{ $row->keterangan ? \Illuminate\Support\Str::limit($row->keterangan, 80) : '—' }}</td>
                                <td class="px-5 py-3 text-right">
                                    @can('update', $row)
                                        <a href="{{ route('kesiswaan.reward.edit', $row) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada catatan.') }}</td>
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
