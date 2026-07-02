<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Tagihan & pembayaran') }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $siswa->nama }}
                    @if ($siswa->kelas)
                        · {{ __('Kelas') }} {{ $siswa->kelas->tingkat }} {{ $siswa->kelas->nama }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('wali.show', $siswa) }}" class="btn-nu">{{ __('Ringkasan siswa') }}</a>
                <a href="{{ route('wali.index') }}" class="btn-nu">{{ __('Anak saya') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total tagihan') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-gray-900">{{ number_format($stats['total_tagihan']) }}</div>
                </div>
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Belum lunas') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-nu-primary">{{ number_format($stats['belum_lunas']) }}</div>
                </div>
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sisa tagihan') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-amber-700">@include('keuangan.partials.rupiah', ['value' => $stats['total_sisa'], 'decimals' => 0])</div>
                </div>
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total dibayar') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-emerald-700">@include('keuangan.partials.rupiah', ['value' => $stats['total_dibayar'], 'decimals' => 0])</div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-900">{{ __('Daftar tagihan') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Rincian tagihan per periode dan jenis biaya.') }}</p>
                    </div>
                    <form method="GET" action="{{ route('wali.tagihan.index', $siswa) }}" class="flex flex-wrap items-center gap-2">
                        <select name="status" class="rounded-xl border-gray-200 text-sm shadow-sm focus:border-nu-primary focus:ring-nu-primary/25" onchange="this.form.submit()">
                            <option value="">{{ __('Semua status') }}</option>
                            @foreach (\App\Models\Tagihan::STATUS_OPTIONS as $st)
                                <option value="{{ $st }}" @selected($status === $st)>
                                    {{ match ($st) {
                                        'paid' => __('Lunas'),
                                        'partial' => __('Sebagian'),
                                        default => __('Belum lunas'),
                                    } }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead>
                            <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                <th class="py-3 pr-4">{{ __('Periode') }}</th>
                                <th class="py-3 pr-4">{{ __('Jenis') }}</th>
                                <th class="py-3 pr-4 text-right">{{ __('Tagihan') }}</th>
                                <th class="py-3 pr-4 text-right">{{ __('Dibayar') }}</th>
                                <th class="py-3 pr-4 text-right">{{ __('Sisa') }}</th>
                                <th class="py-3 pr-4">{{ __('Status') }}</th>
                                <th class="py-3 pr-4 text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($tagihans as $t)
                                @php $sisaRow = max(0, (float) $t->jumlah - (float) ($t->total_dibayar ?? 0)); @endphp
                                <tr class="text-gray-700">
                                    <td class="py-3 pr-4 font-mono font-semibold text-gray-900">{{ $t->periode }}</td>
                                    <td class="py-3 pr-4">{{ $t->jenis }}</td>
                                    <td class="py-3 pr-4 text-right font-mono">@include('keuangan.partials.rupiah', ['value' => $t->jumlah])</td>
                                    <td class="py-3 pr-4 text-right font-mono text-emerald-800">@include('keuangan.partials.rupiah', ['value' => $t->total_dibayar ?? 0])</td>
                                    <td class="py-3 pr-4 text-right font-mono font-semibold">@include('keuangan.partials.rupiah', ['value' => $sisaRow])</td>
                                    <td class="py-3 pr-4">@include('keuangan.partials.tagihan-status', ['status' => $t->status])</td>
                                    <td class="py-3 pr-4 text-right whitespace-nowrap">
                                        <a href="{{ route('wali.tagihan.show', [$siswa, $t]) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Detail') }}</a>
                                        @can('view', $t)
                                            <span class="mx-1 text-gray-300">·</span>
                                            <a href="{{ route('tagihan.invoice.pdf', $t) }}" class="text-sm font-semibold text-gray-600 hover:underline">{{ __('PDF') }}</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-gray-500">{{ __('Belum ada tagihan.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">{{ __('Riwayat pembayaran') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('50 transaksi terakhir yang tercatat di sekolah.') }}</p>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead>
                            <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                <th class="py-3 pr-4">{{ __('Tanggal') }}</th>
                                <th class="py-3 pr-4">{{ __('Tagihan') }}</th>
                                <th class="py-3 pr-4 text-right">{{ __('Jumlah') }}</th>
                                <th class="py-3 pr-4">{{ __('Metode') }}</th>
                                <th class="py-3 pr-4">{{ __('Referensi') }}</th>
                                <th class="py-3 pr-4 text-right">{{ __('Kwitansi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($pembayarans as $p)
                                <tr class="text-gray-700">
                                    <td class="py-3 pr-4 font-mono whitespace-nowrap">{{ \App\Support\DateTimeFormat::datetime($p->dibayar_pada) }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="font-semibold text-gray-900">{{ $p->tagihan?->jenis }}</span>
                                        <span class="text-gray-500">· {{ $p->tagihan?->periode }}</span>
                                    </td>
                                    <td class="py-3 pr-4 text-right font-mono font-semibold text-emerald-800">@include('keuangan.partials.rupiah', ['value' => $p->jumlah])</td>
                                    <td class="py-3 pr-4">{{ $p->metode }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $p->referensi ?: '—' }}</td>
                                    <td class="py-3 pr-4 text-right">
                                        @can('view', $p)
                                            <a href="{{ route('pembayaran.kwitansi.pdf', $p) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Unduh PDF') }}</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-500">{{ __('Belum ada pembayaran tercatat.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
