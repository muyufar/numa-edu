<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl">
            <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Keuangan') }}</h2>
            <p class="mt-1 max-w-2xl text-sm leading-relaxed text-gray-600">{{ __('Ringkasan cepat, lalu pilih modul. Menu lengkap juga tersedia di sidebar «Keuangan».') }}</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-8 pb-2">
        {{-- Ringkasan angka --}}
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex flex-col justify-between rounded-2xl border border-amber-100/80 bg-gradient-to-b from-amber-50/80 to-white p-4 shadow-sm ring-1 ring-amber-900/5 sm:p-5">
                <div class="text-[11px] font-bold uppercase tracking-wide text-amber-800/80">{{ __('Belum lunas') }}</div>
                <div class="mt-2 font-mono text-2xl font-extrabold tabular-nums text-amber-900">{{ number_format($stats['tagihan_unpaid'] + $stats['tagihan_partial']) }}</div>
                <div class="mt-1 text-xs text-gray-600">{{ __('tagihan') }}</div>
            </div>
            <div class="flex flex-col justify-between rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
                <div class="text-[11px] font-bold uppercase tracking-wide text-gray-500">{{ __('Sisa piutang') }}</div>
                <div class="mt-2 font-mono text-2xl font-extrabold tabular-nums text-nu-primary">Rp {{ number_format((int) round($outstanding), 0, ',', '.') }}</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('Belum dibayar penuh') }}</div>
            </div>
            <div class="flex flex-col justify-between rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
                <div class="text-[11px] font-bold uppercase tracking-wide text-gray-500">{{ __('Pemasukan bulan ini') }}</div>
                <div class="mt-2 font-mono text-2xl font-extrabold tabular-nums text-emerald-700">Rp {{ number_format((int) round($pemasukanBulanIni), 0, ',', '.') }}</div>
                <div class="mt-1 font-mono text-xs text-gray-500">{{ now()->format('Y-m') }}</div>
            </div>
            <div class="flex flex-col justify-between rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
                <div class="text-[11px] font-bold uppercase tracking-wide text-gray-500">{{ __('Lunas') }}</div>
                <div class="mt-2 font-mono text-2xl font-extrabold tabular-nums text-gray-900">{{ number_format($stats['tagihan_paid']) }}</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('tagihan') }}</div>
            </div>
        </div>

        {{-- Tagihan & pembayaran --}}
        <section class="rounded-2xl border border-gray-100/90 bg-white p-5 shadow-sm ring-1 ring-black/[0.04] sm:p-6" aria-labelledby="keuangan-alur-heading">
            <header id="keuangan-alur-heading" class="mb-5 max-w-3xl">
                <h3 class="text-base font-bold text-gray-900">{{ __('Tagihan & pembayaran') }}</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-gray-600">{{ __('Alur umum: proses atau daftar tagihan, lalu tunggakan dan rekap.') }}</p>
            </header>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('keuangan.proses.index') }}" class="group flex h-full min-h-[148px] flex-col rounded-xl border border-gray-100 bg-white p-4 shadow-sm ring-1 ring-black/[0.03] transition hover:border-emerald-200 hover:ring-emerald-500/20 sm:p-5">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-700 ring-1 ring-emerald-500/15">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span class="mt-3 font-semibold text-gray-900 group-hover:text-nu-primary">{{ __('Proses pembayaran') }}</span>
                    <span class="mt-1 flex-1 text-sm leading-snug text-gray-600">{{ __('Siswa, periode, generate & bayar.') }}</span>
                    <span class="mt-4 text-sm font-semibold text-emerald-700">{{ __('Buka') }} →</span>
                </a>
                <a href="{{ route('tagihan.index') }}" class="group flex h-full min-h-[148px] flex-col rounded-xl border border-gray-100 bg-white p-4 shadow-sm ring-1 ring-black/[0.03] transition hover:border-nu-primary/25 hover:ring-nu-primary/15 sm:p-5">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-nu-primary/10 text-nu-primary ring-1 ring-nu-primary/15">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <span class="mt-3 font-semibold text-gray-900 group-hover:text-nu-primary">{{ __('Daftar tagihan') }}</span>
                    <span class="mt-1 flex-1 text-sm leading-snug text-gray-600">{{ __('Semua tagihan & riwayat bayar per siswa.') }}</span>
                    <span class="mt-4 text-sm font-semibold text-nu-primary">{{ __('Buka') }} →</span>
                </a>
                <a href="{{ route('keuangan.tunggakan.index') }}" class="group flex h-full min-h-[148px] flex-col rounded-xl border border-gray-100 bg-white p-4 shadow-sm ring-1 ring-black/[0.03] transition hover:border-orange-200 hover:ring-orange-500/20 sm:p-5">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-500/10 text-orange-800 ring-1 ring-orange-500/15">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span class="mt-3 font-semibold text-gray-900 group-hover:text-nu-primary">{{ __('Tunggakan') }}</span>
                    <span class="mt-1 flex-1 text-sm leading-snug text-gray-600">{{ __('Belum lunas, filter & CSV.') }}</span>
                    <span class="mt-4 text-sm font-semibold text-orange-800">{{ __('Buka') }} →</span>
                </a>
                <a href="{{ route('keuangan.rekap.index') }}" class="group flex h-full min-h-[148px] flex-col rounded-xl border border-gray-100 bg-white p-4 shadow-sm ring-1 ring-black/[0.03] transition hover:border-sky-200 hover:ring-sky-500/20 sm:p-5">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-700 ring-1 ring-sky-500/15">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </span>
                    <span class="mt-3 font-semibold text-gray-900 group-hover:text-nu-primary">{{ __('Rekap keuangan') }}</span>
                    <span class="mt-1 flex-1 text-sm leading-snug text-gray-600">{{ __('Per periode, siswa, & kelas.') }}</span>
                    <span class="mt-4 text-sm font-semibold text-sky-800">{{ __('Buka') }} →</span>
                </a>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Pengaturan --}}
            <section class="rounded-2xl border border-gray-100/90 bg-white p-5 shadow-sm ring-1 ring-black/[0.04] sm:p-6 lg:col-span-1" aria-labelledby="keuangan-master-heading">
                <header id="keuangan-master-heading" class="mb-4">
                    <h3 class="text-base font-bold text-gray-900">{{ __('Jenis pembayaran') }}</h3>
                    <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ __('Master kewajiban (SPP, dll.) sebelum generate tagihan.') }}</p>
                </header>
                <a href="{{ route('keuangan.kewajiban.index') }}" class="group flex min-h-[120px] flex-col rounded-xl border border-amber-100/90 bg-amber-50/30 p-4 transition hover:border-amber-200 hover:bg-amber-50/50 sm:p-5">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/15 text-amber-800 ring-1 ring-amber-500/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h10"/></svg>
                    </span>
                    <span class="mt-3 font-semibold text-gray-900 group-hover:text-nu-primary">{{ __('Master kewajiban') }}</span>
                    <span class="mt-4 text-sm font-semibold text-amber-900/90">{{ __('Kelola') }} →</span>
                </a>
            </section>

            {{-- Kas + Akuntansi (2 kolom di desktop) --}}
            <div class="space-y-6 lg:col-span-2">
                <section class="rounded-2xl border border-gray-100/90 bg-white p-5 shadow-sm ring-1 ring-black/[0.04] sm:p-6" aria-labelledby="keuangan-kas-heading">
                    <header id="keuangan-kas-heading" class="mb-4 max-w-2xl">
                        <h3 class="text-base font-bold text-gray-900">{{ __('Kas') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('Mutasi kas & pengeluaran.') }}</p>
                    </header>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <a href="{{ route('keuangan.buku-kas.index') }}" class="group flex min-h-[120px] flex-col rounded-xl border border-gray-100 bg-gray-50/40 p-4 transition hover:border-violet-200 hover:bg-white sm:p-5">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/10 text-violet-700 ring-1 ring-violet-500/15">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <span class="mt-3 font-semibold text-gray-900 group-hover:text-nu-primary">{{ __('Buku kas') }}</span>
                            <span class="mt-4 text-sm font-semibold text-violet-800">{{ __('Buka') }} →</span>
                        </a>
                        <a href="{{ route('keuangan.pengeluaran-kas.index') }}" class="group flex min-h-[120px] flex-col rounded-xl border border-gray-100 bg-gray-50/40 p-4 transition hover:border-rose-200 hover:bg-white sm:p-5">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/10 text-rose-700 ring-1 ring-rose-500/15">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3z"/></svg>
                            </span>
                            <span class="mt-3 font-semibold text-gray-900 group-hover:text-nu-primary">{{ __('Pengeluaran kas') }}</span>
                            <span class="mt-4 text-sm font-semibold text-rose-800">{{ __('Buka') }} →</span>
                        </a>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-100/90 bg-white p-5 shadow-sm ring-1 ring-black/[0.04] sm:p-6" aria-labelledby="keuangan-akuntansi-heading">
                    <header id="keuangan-akuntansi-heading" class="mb-4 max-w-2xl">
                        <h3 class="text-base font-bold text-gray-900">{{ __('Akuntansi & akun') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('Jurnal & COA.') }}</p>
                    </header>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <a href="{{ route('akuntansi.index') }}" class="group flex min-h-[120px] flex-col rounded-xl border border-gray-100 bg-gray-50/40 p-4 transition hover:border-amber-200 hover:bg-white sm:p-5">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-800 ring-1 ring-amber-500/15">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10H9m8-14H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2z"/></svg>
                            </span>
                            <span class="mt-3 font-semibold text-gray-900 group-hover:text-nu-primary">{{ __('Jurnal umum') }}</span>
                            <span class="mt-4 text-sm font-semibold text-amber-900/90">{{ __('Buka') }} →</span>
                        </a>
                        <a href="{{ route('keuangan.coa.index') }}" class="group flex min-h-[120px] flex-col rounded-xl border border-gray-100 bg-gray-50/40 p-4 transition hover:border-teal-200 hover:bg-white sm:p-5">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-teal-500/10 text-teal-800 ring-1 ring-teal-500/15">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            </span>
                            <span class="mt-3 font-semibold text-gray-900 group-hover:text-nu-primary">{{ __('Daftar akun (COA)') }}</span>
                            <span class="mt-4 text-sm font-semibold text-teal-900/90">{{ __('Buka') }} →</span>
                        </a>
                    </div>
                </section>
            </div>
        </div>

        @if (auth()->user()->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']))
            <section class="rounded-2xl border border-sky-100/90 bg-gradient-to-br from-sky-50/60 to-white p-5 shadow-sm ring-1 ring-sky-900/5 sm:p-6" aria-labelledby="keuangan-ekspor-heading">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <header id="keuangan-ekspor-heading" class="max-w-xl">
                        <h3 class="text-base font-bold text-gray-900">{{ __('Ekspor & CSV') }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ __('Unduh data ke Excel — termasuk tagihan, pembayaran, dan laporan lain.') }}</p>
                    </header>
                    <a href="{{ route('laporan.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                        {{ __('Buka pelaporan') }}
                    </a>
                </div>
            </section>
        @endif

        @if ($recentTagihan->isNotEmpty())
            <div class="overflow-hidden rounded-2xl border border-gray-100/90 bg-white shadow-sm ring-1 ring-black/[0.04]">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/80 px-5 py-3.5">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Tagihan terbaru') }}</h3>
                    <a href="{{ route('tagihan.index') }}" class="text-xs font-semibold text-nu-primary hover:underline">{{ __('Lihat semua') }}</a>
                </div>
                <ul class="divide-y divide-gray-100">
                    @foreach ($recentTagihan as $tg)
                        @php
                            $totalBayar = (float) ($tg->total_bayar ?? 0);
                            $sisa = max(0, (float) $tg->jumlah - $totalBayar);
                            $statusBadgeClass = match ($tg->status) {
                                'paid' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/15',
                                'partial' => 'bg-amber-50 text-amber-900 ring-amber-600/15',
                                default => 'bg-red-50 text-red-800 ring-red-600/15',
                            };
                        @endphp
                        <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 text-sm">
                            <div class="min-w-0">
                                <a href="{{ route('tagihan.show', $tg) }}" class="font-semibold text-gray-900 hover:text-nu-primary hover:underline">{{ $tg->siswa?->nama ?? '-' }}</a>
                                <div class="text-xs text-gray-500">{{ $tg->jenis }} · {{ $tg->periode }}</div>
                                @if ($tg->status !== 'paid')
                                    <div class="mt-1 text-xs text-gray-600">{{ __('Sisa') }}: <span class="font-mono font-semibold text-amber-800">Rp {{ number_format((int) round($sisa), 0, ',', '.') }}</span></div>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="font-mono text-sm text-gray-800" title="{{ __('Nominal tagihan') }}">Rp {{ number_format((float) $tg->jumlah, 0, ',', '.') }}</span>
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide ring-1 {{ $statusBadgeClass }}">{{ $tg->status }}</span>
                                <a href="{{ route('tagihan.show', $tg) }}" class="text-xs font-bold text-nu-primary hover:underline">{{ __('Detail') }}</a>
                                @can('update', $tg)
                                    <a href="{{ route('tagihan.edit', $tg) }}" class="text-xs font-semibold text-gray-600 hover:text-nu-primary hover:underline">{{ __('Ubah') }}</a>
                                @endcan
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-app-layout>
