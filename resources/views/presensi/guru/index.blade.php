<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Presensi guru') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Riwayat input presensi staf pengajar.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('presensi.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Ringkasan absensi') }}
                </a>
                @can('create', \App\Models\PresensiGuru::class)
                    <a href="{{ route('presensi.scan.show', 'guru') }}" class="inline-flex items-center justify-center rounded-xl border border-nu-primary/30 bg-nu-primary/5 px-4 py-2.5 text-sm font-semibold text-nu-primary shadow-sm hover:bg-nu-primary/10">
                        {{ __('Scan barcode/wajah') }}
                    </a>
                    <a href="{{ route('presensi.guru.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Input manual') }}
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
            <form method="GET" action="{{ route('presensi.guru.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1 sm:max-w-xs">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tanggal') }}</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Terapkan') }}
                    </button>
                    <a href="{{ route('presensi.guru.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Data presensi') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $rows->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Tanggal') }}</th>
                            <th class="px-5 py-3">{{ __('Guru') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3">{{ __('Metode') }}</th>
                            <th class="px-5 py-3">{{ __('Jam') }}</th>
                            <th class="px-5 py-3">{{ __('Keterangan') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($rows as $p)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-mono text-gray-900">{{ $p->tanggal?->format('Y-m-d') }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $p->guru?->nama ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800 ring-1 ring-gray-200">
                                        @include('presensi.partials.status-label', ['status' => $p->status])
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    @include('presensi.partials.metode-label', ['metode' => $p->metode ?? 'manual'])
                                </td>
                                <td class="px-5 py-3 font-mono text-gray-600">{{ $p->jam_masuk ? substr((string) $p->jam_masuk, 0, 5) : '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $p->keterangan ?: '—' }}</td>
                                <td class="px-5 py-3">
                                    @can('delete', $p)
                                        <form method="POST" action="{{ route('presensi.guru.destroy', $p) }}" class="flex justify-end" onsubmit="return confirm('{{ __('Hapus baris presensi ini?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">
                                    {{ __('Belum ada data. Sesuaikan filter atau lakukan input presensi.') }}
                                </td>
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
