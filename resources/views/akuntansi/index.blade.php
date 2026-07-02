<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Akuntansi') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Jurnal umum untuk pencatatan transaksi sekolah.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('akuntansi.jurnal.index') }}" class="btn-nu">{{ __('Daftar jurnal') }}</a>
                <a href="{{ route('akuntansi.jurnal.export') }}" class="btn-nu">{{ __('CSV jurnal') }}</a>
                @can('create', \App\Models\Tagihan::class)
                    <a href="{{ route('akuntansi.jurnal.create') }}" class="btn-nu-primary">{{ __('Jurnal manual') }}</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="grid gap-4 lg:grid-cols-2">
        <a href="{{ route('keuangan.buku-kas.index') }}" class="group rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:ring-nu-primary/25">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-violet-500/10 text-violet-700 ring-1 ring-violet-500/15">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 group-hover:text-nu-primary">{{ __('Buku kas') }}</div>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Lihat mutasi akun Kas dan saldo berjalan.') }}</p>
                    <div class="mt-3 text-sm font-semibold text-nu-primary">{{ __('Buka buku kas') }} →</div>
                </div>
            </div>
        </a>
        <a href="{{ route('keuangan.pemasukan-kas.index') }}" class="group rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:ring-nu-primary/25">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-700 ring-1 ring-emerald-500/15">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 group-hover:text-nu-primary">{{ __('Pemasukan kas') }}</div>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Hibah, bantuan, sewa — jurnal debit kas & kredit pendapatan.') }}</p>
                    <div class="mt-3 text-sm font-semibold text-nu-primary">{{ __('Buka pemasukan') }} →</div>
                </div>
            </div>
        </a>
        <a href="{{ route('keuangan.pengeluaran-kas.index') }}" class="group rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:ring-nu-primary/25">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-500/10 text-rose-700 ring-1 ring-rose-500/15">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3z"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 group-hover:text-nu-primary">{{ __('Pengeluaran kas') }}</div>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Catat pengeluaran dengan jurnal debit beban & kredit kas.') }}</p>
                    <div class="mt-3 text-sm font-semibold text-nu-primary">{{ __('Buka pengeluaran') }} →</div>
                </div>
            </div>
        </a>
        <a href="{{ route('keuangan.index') }}" class="group rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:ring-nu-primary/25">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-nu-primary/10 text-nu-primary ring-1 ring-nu-primary/15">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 group-hover:text-nu-primary">{{ __('Keuangan (Tagihan/Pembayaran)') }}</div>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Kembali ke modul tagihan dan pembayaran.') }}</p>
                    <div class="mt-3 text-sm font-semibold text-nu-primary">{{ __('Buka keuangan') }} →</div>
                </div>
            </div>
        </a>

        <a href="{{ route('keuangan.coa.index') }}" class="group rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:ring-nu-primary/25">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-500/10 text-teal-700 ring-1 ring-teal-500/15">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 group-hover:text-nu-primary">{{ __('Daftar akun (COA)') }}</div>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Kelola kode akun, tipe, dan status untuk jurnal.') }}</p>
                    <div class="mt-3 text-sm font-semibold text-nu-primary">{{ __('Buka COA') }} →</div>
                </div>
            </div>
        </a>

        <div class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
            <div class="font-bold text-gray-900">{{ __('Jurnal terbaru') }}</div>
            <p class="mt-1 text-sm text-gray-600">{{ __('Otomatis terbentuk saat pembayaran dicatat.') }}</p>

            @if ($recentJurnal->isEmpty())
                <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50 px-4 py-6 text-sm text-gray-600">
                    {{ __('Belum ada jurnal.') }}
                </div>
            @else
                <div class="mt-4 space-y-3">
                    @foreach ($recentJurnal as $j)
                        <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <div class="font-semibold text-gray-900">{{ \App\Support\DateTimeFormat::date($j->tanggal) }}</div>
                                <div class="text-xs text-gray-500">#{{ $j->id }}</div>
                            </div>
                            <div class="mt-1 text-xs text-gray-600">{{ $j->keterangan ?? '-' }}</div>
                            <div class="mt-2 grid gap-1 text-xs text-gray-700">
                                @foreach ($j->lines as $l)
                                    <div class="flex items-center justify-between gap-3 font-mono">
                                        <span class="truncate">{{ $l->akun?->kode }} {{ $l->akun?->nama }}</span>
                                        <span>
                                            {{ number_format((float) $l->debit, 0, ',', '.') }} / {{ number_format((float) $l->kredit, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

