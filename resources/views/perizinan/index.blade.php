<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Perizinan siswa') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Izin, sakit, dan dispensasi.') }}</p>
            </div>
            @can('create', \App\Models\Perizinan::class)
                <a href="{{ route('perizinan.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Ajukan') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
            <form method="GET" action="{{ route('perizinan.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 sm:items-end">
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
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</label>
                    <select name="status" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach (\App\Models\Perizinan::STATUS_OPTIONS as $st)
                            <option value="{{ $st }}" {{ (string) $status === (string) $st ? 'selected' : '' }}>{{ \App\Models\Perizinan::statusLabel($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tanggal') }}</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Terapkan') }}</button>
                    <a href="{{ route('perizinan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Reset') }}</a>
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
                            <th class="px-5 py-3">{{ __('Jenis') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($rows as $row)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ $row->tanggal?->format('Y-m-d') }}</td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $row->siswa?->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $row->siswa?->nis }}</div>
                                </td>
                                <td class="px-5 py-3">{{ \App\Models\Perizinan::jenisLabel($row->jenis) }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1
                                        {{ $row->status === 'approved' ? 'bg-emerald-50 text-emerald-800 ring-emerald-200/80' : '' }}
                                        {{ $row->status === 'rejected' ? 'bg-red-50 text-red-800 ring-red-200/80' : '' }}
                                        {{ $row->status === 'pending' ? 'bg-amber-50 text-amber-900 ring-amber-200/80' : '' }}">
                                        {{ \App\Models\Perizinan::statusLabel($row->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @can('update', $row)
                                        <a href="{{ route('perizinan.edit', $row) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada data.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($rows->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $rows->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
