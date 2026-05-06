<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Tagihan') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Daftar tagihan per siswa dan status pembayaran.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('keuangan.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Ringkasan keuangan') }}
                </a>
                <a href="{{ route('laporan.tagihan-csv', request()->query()) }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Export tagihan (CSV)') }}
                </a>
                <a href="{{ route('laporan.pembayaran-csv', request()->query()) }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Export pembayaran (CSV)') }}
                </a>
                @can('create', \App\Models\Tagihan::class)
                    <a href="{{ route('tagihan.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Buat tagihan') }}
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
            <form method="GET" action="{{ route('tagihan.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6 sm:items-end">
                <div class="lg:col-span-1">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Siswa') }}</label>
                    <select name="siswa_id" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach ($siswaFilterOptions as $s)
                            <option value="{{ $s->id }}" {{ (string) $siswaId === (string) $s->id ? 'selected' : '' }}>
                                {{ $s->nama }} ({{ $s->nis }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kelas') }}</label>
                    <select name="kelas_id" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) $kelasId === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->nama }} {{ $k->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</label>
                    <select name="status" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach (\App\Models\Tagihan::STATUS_OPTIONS as $st)
                            <option value="{{ $st }}" {{ (string) $status === (string) $st ? 'selected' : '' }}>
                                {{ match ($st) {
                                    'unpaid' => __('Belum lunas'),
                                    'partial' => __('Sebagian'),
                                    'paid' => __('Lunas'),
                                    default => $st,
                                } }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Periode dari') }}</label>
                    <input type="text" name="periode_from" value="{{ $periodeFrom }}" maxlength="7" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="2026-01" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Periode sampai') }}</label>
                    <input type="text" name="periode_to" value="{{ $periodeTo }}" maxlength="7" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="2026-12" />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Terapkan') }}
                    </button>
                    <a href="{{ route('tagihan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Data tagihan') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $tagihans->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Siswa') }}</th>
                            <th class="px-5 py-3">{{ __('Jenis') }}</th>
                            <th class="px-5 py-3">{{ __('Periode') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Tagihan') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Dibayar') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Sisa') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($tagihans as $t)
                            @php
                                $dibayar = (float) ($t->total_dibayar ?? 0);
                                $sisa = max(0, (float) $t->jumlah - $dibayar);
                            @endphp
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $t->siswa?->nama ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-800">{{ $t->jenis }}</td>
                                <td class="px-5 py-3 font-mono text-gray-700">{{ $t->periode }}</td>
                                <td class="px-5 py-3 text-right font-mono text-gray-900">@include('keuangan.partials.rupiah', ['value' => $t->jumlah])</td>
                                <td class="px-5 py-3 text-right font-mono text-emerald-800">@include('keuangan.partials.rupiah', ['value' => $dibayar])</td>
                                <td class="px-5 py-3 text-right font-mono font-semibold text-gray-900">@include('keuangan.partials.rupiah', ['value' => $sisa])</td>
                                <td class="px-5 py-3">@include('keuangan.partials.tagihan-status', ['status' => $t->status])</td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @can('view', $t)
                                            <a href="{{ route('tagihan.show', $t) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                                {{ __('Detail') }}
                                            </a>
                                        @endcan
                                        @can('update', $t)
                                            <a href="{{ route('tagihan.edit', $t) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                                {{ __('Edit') }}
                                            </a>
                                        @endcan
                                        @can('delete', $t)
                                            <form method="POST" action="{{ route('tagihan.destroy', $t) }}" onsubmit="return confirm('{{ __('Hapus tagihan beserta riwayat pembayarannya?') }}')">
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
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500">
                                    {{ __('Belum ada tagihan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($tagihans->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $tagihans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
