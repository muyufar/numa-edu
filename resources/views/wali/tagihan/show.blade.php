<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Detail tagihan') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $siswa->nama }} · {{ $tagihan->jenis }} · {{ $tagihan->periode }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('view', $tagihan)
                    <a href="{{ route('tagihan.invoice.pdf', $tagihan) }}" class="inline-flex items-center rounded-xl border border-nu-primary/30 bg-white px-4 py-2.5 text-sm font-semibold text-nu-primary shadow-sm hover:bg-nu-primary/5">
                        {{ __('Unduh invoice PDF') }}
                    </a>
                @endcan
                <a href="{{ route('wali.tagihan.index', $siswa) }}" class="btn-nu">{{ __('Semua tagihan') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:col-span-1">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</div>
                    <div class="mt-2">@include('keuangan.partials.tagihan-status', ['status' => $tagihan->status])</div>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">{{ __('Jumlah tagihan') }}</dt>
                            <dd class="mt-0.5 font-mono font-semibold text-gray-900">@include('keuangan.partials.rupiah', ['value' => $tagihan->jumlah])</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Sudah dibayar') }}</dt>
                            <dd class="mt-0.5 font-mono text-emerald-800">@include('keuangan.partials.rupiah', ['value' => $tagihan->totalDibayar()])</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Sisa') }}</dt>
                            <dd class="mt-0.5 font-mono font-bold text-gray-900">@include('keuangan.partials.rupiah', ['value' => $sisa])</dd>
                        </div>
                        @if ($tagihan->jatuh_tempo)
                            <div>
                                <dt class="text-gray-500">{{ __('Jatuh tempo') }}</dt>
                                <dd class="mt-0.5 font-mono text-gray-900">{{ \App\Support\DateTimeFormat::date($tagihan->jatuh_tempo) }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:col-span-2">
                    <div class="text-lg font-extrabold text-gray-900">{{ __('Riwayat pembayaran') }}</div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                    <th class="py-2 pr-4">{{ __('Tanggal') }}</th>
                                    <th class="py-2 pr-4 text-right">{{ __('Jumlah') }}</th>
                                    <th class="py-2 pr-4">{{ __('Metode') }}</th>
                                    <th class="py-2 pr-4">{{ __('Referensi') }}</th>
                                    <th class="py-2 pr-4 text-right">{{ __('Kwitansi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse ($tagihan->pembayarans as $p)
                                    <tr class="text-gray-700">
                                        <td class="py-3 pr-4 font-mono whitespace-nowrap">{{ \App\Support\DateTimeFormat::datetime($p->dibayar_pada) }}</td>
                                        <td class="py-3 pr-4 text-right font-mono font-medium text-gray-900">@include('keuangan.partials.rupiah', ['value' => $p->jumlah])</td>
                                        <td class="py-3 pr-4">
                                            {{ match ($p->metode) {
                                                'tunai' => __('Tunai'),
                                                'transfer' => __('Transfer bank'),
                                                'virtual' => __('Virtual account'),
                                                'lainnya' => __('Lainnya'),
                                                default => $p->metode,
                                            } }}
                                        </td>
                                        <td class="py-3 pr-4 text-gray-600">{{ $p->referensi ?: '—' }}</td>
                                        <td class="py-3 pr-4 text-right">
                                            @can('view', $p)
                                                <a href="{{ route('pembayaran.kwitansi.pdf', $p) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Unduh PDF') }}</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-500">{{ __('Belum ada pembayaran untuk tagihan ini.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($sisa > 0.0001)
                        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            {{ __('Pembayaran dilakukan melalui sekolah (kasir/TU). Hubungi sekolah untuk melunasi sisa tagihan.') }}
                        </div>
                    @else
                        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                            {{ __('Tagihan sudah lunas.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
