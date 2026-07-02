<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Laporan keuangan anak') }}</h2>
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
            {{-- Status tunggakan --}}
            @if ($summary['has_tunggakan'])
                <div class="rounded-3xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-6 shadow-sm ring-1 ring-amber-200/60 sm:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex gap-4">
                            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 ring-1 ring-amber-200">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wide text-amber-800">{{ __('Status tunggakan') }}</div>
                                <div class="mt-1 text-2xl font-extrabold text-amber-950">{{ __('Ada tunggakan') }}</div>
                                <p class="mt-2 max-w-xl text-sm text-amber-900/90">
                                    {{ __('Anak Anda memiliki :count tagihan yang belum lunas. Total sisa yang harus dibayar:', ['count' => $summary['jumlah_belum_lunas']]) }}
                                    <span class="font-bold">@include('keuangan.partials.rupiah', ['value' => $summary['total_sisa'], 'decimals' => 0])</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-sm ring-1 ring-emerald-200/60 sm:p-8">
                    <div class="flex gap-4">
                        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wide text-emerald-800">{{ __('Status tunggakan') }}</div>
                            <div class="mt-1 text-2xl font-extrabold text-emerald-950">{{ __('Tidak ada tunggakan') }}</div>
                            <p class="mt-2 text-sm text-emerald-900/90">{{ __('Semua tagihan tercatat sudah lunas. Terima kasih.') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Statistik --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total tagihan') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-gray-900">{{ number_format($summary['stats']['total_tagihan']) }}</div>
                </div>
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Belum lunas') }}</div>
                    <div class="mt-2 text-2xl font-extrabold {{ $summary['has_tunggakan'] ? 'text-amber-700' : 'text-gray-400' }}">{{ number_format($summary['stats']['belum_lunas']) }}</div>
                </div>
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total sisa bayar') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-nu-primary">@include('keuangan.partials.rupiah', ['value' => $summary['total_sisa'], 'decimals' => 0])</div>
                </div>
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total sudah dibayar') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-emerald-700">@include('keuangan.partials.rupiah', ['value' => $summary['stats']['total_dibayar'], 'decimals' => 0])</div>
                </div>
            </div>

            {{-- Yang harus dibayar --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">{{ __('Yang harus dibayar') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Daftar kewajiban yang belum lunas. Pembayaran dilakukan melalui kasir/TU sekolah.') }}</p>
                </div>

                @if ($summary['harus_bayar']->isNotEmpty())
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                    <th class="py-3 pr-4">{{ __('Jenis biaya') }}</th>
                                    <th class="py-3 pr-4">{{ __('Periode') }}</th>
                                    <th class="py-3 pr-4 text-right">{{ __('Nominal') }}</th>
                                    <th class="py-3 pr-4 text-right">{{ __('Sisa bayar') }}</th>
                                    <th class="py-3 pr-4">{{ __('Jatuh tempo') }}</th>
                                    <th class="py-3 pr-4">{{ __('Status') }}</th>
                                    <th class="py-3 pr-4 text-right">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($summary['harus_bayar'] as $row)
                                    @php /** @var \App\Models\Tagihan $t */ $t = $row['tagihan']; @endphp
                                    <tr class="{{ $row['is_overdue'] ? 'bg-red-50/50' : '' }}">
                                        <td class="py-3 pr-4 font-semibold text-gray-900">{{ $t->jenis }}</td>
                                        <td class="py-3 pr-4 font-mono">{{ $t->periode }}</td>
                                        <td class="py-3 pr-4 text-right font-mono">@include('keuangan.partials.rupiah', ['value' => $t->jumlah])</td>
                                        <td class="py-3 pr-4 text-right font-mono font-bold text-amber-800">@include('keuangan.partials.rupiah', ['value' => $row['sisa']])</td>
                                        <td class="py-3 pr-4 font-mono text-gray-700">
                                            @if ($t->jatuh_tempo)
                                                {{ \App\Support\DateTimeFormat::date($t->jatuh_tempo) }}
                                                @if ($row['is_overdue'])
                                                    <span class="ml-1 inline-flex rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold uppercase text-red-800">{{ __('Lewat') }}</span>
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4">@include('keuangan.partials.tagihan-status', ['status' => $t->status])</td>
                                        <td class="py-3 pr-4 text-right whitespace-nowrap">
                                            <a href="{{ route('wali.tagihan.show', [$siswa, $t]) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Detail') }}</a>
                                            @can('view', $t)
                                                <span class="mx-1 text-gray-300">·</span>
                                                <a href="{{ route('tagihan.invoice.pdf', $t) }}" class="text-sm font-semibold text-gray-600 hover:underline">{{ __('Invoice') }}</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-gray-200 bg-gray-50/80">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-600">{{ __('Total sisa') }}</td>
                                    <td class="px-4 py-3 text-right font-mono text-base font-extrabold text-nu-primary">@include('keuangan.partials.rupiah', ['value' => $summary['total_sisa'], 'decimals' => 0])</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50/60 px-4 py-8 text-center text-sm text-emerald-900">
                        {{ __('Tidak ada tagihan yang perlu dibayar saat ini.') }}
                    </div>
                @endif
            </div>

            {{-- Semua tagihan --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">{{ __('Semua tagihan') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Riwayat lengkap tagihan termasuk yang sudah lunas.') }}</p>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($summary['tagihans'] as $t)
                                @php $sisaRow = $t->sisa(); @endphp
                                <tr class="text-gray-700">
                                    <td class="py-3 pr-4 font-mono font-semibold">{{ $t->periode }}</td>
                                    <td class="py-3 pr-4">
                                        <a href="{{ route('wali.tagihan.show', [$siswa, $t]) }}" class="font-medium text-nu-primary hover:underline">{{ $t->jenis }}</a>
                                    </td>
                                    <td class="py-3 pr-4 text-right font-mono">@include('keuangan.partials.rupiah', ['value' => $t->jumlah])</td>
                                    <td class="py-3 pr-4 text-right font-mono text-emerald-800">@include('keuangan.partials.rupiah', ['value' => $t->total_dibayar ?? $t->totalDibayar()])</td>
                                    <td class="py-3 pr-4 text-right font-mono">@include('keuangan.partials.rupiah', ['value' => $sisaRow])</td>
                                    <td class="py-3 pr-4">@include('keuangan.partials.tagihan-status', ['status' => $t->status])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">{{ __('Belum ada tagihan tercatat.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Riwayat pembayaran terakhir --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">{{ __('Riwayat pembayaran terakhir') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('10 transaksi terakhir yang tercatat di sekolah.') }}</p>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead>
                            <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                <th class="py-3 pr-4">{{ __('Tanggal') }}</th>
                                <th class="py-3 pr-4">{{ __('Tagihan') }}</th>
                                <th class="py-3 pr-4 text-right">{{ __('Jumlah') }}</th>
                                <th class="py-3 pr-4 text-right">{{ __('Kwitansi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($pembayarans as $p)
                                <tr class="text-gray-700">
                                    <td class="py-3 pr-4 font-mono whitespace-nowrap">{{ \App\Support\DateTimeFormat::datetime($p->dibayar_pada) }}</td>
                                    <td class="py-3 pr-4">{{ $p->tagihan?->jenis }} · {{ $p->tagihan?->periode }}</td>
                                    <td class="py-3 pr-4 text-right font-mono font-semibold text-emerald-800">@include('keuangan.partials.rupiah', ['value' => $p->jumlah])</td>
                                    <td class="py-3 pr-4 text-right">
                                        @can('view', $p)
                                            <a href="{{ route('pembayaran.kwitansi.pdf', $p) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Unduh PDF') }}</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500">{{ __('Belum ada pembayaran tercatat.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
