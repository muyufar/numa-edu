<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ $siswa->nama }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('NIS') }}: <span class="font-semibold text-gray-900">{{ $siswa->nis }}</span>
                    @if ($siswa->kelas)
                        · {{ __('Kelas') }}: <span class="font-semibold text-gray-900">{{ $siswa->kelas->tingkat }} {{ $siswa->kelas->nama }}</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('wali.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
            {{-- Dashboard keuangan ringkas --}}
            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-900">{{ __('Laporan keuangan') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Status tunggakan dan kewajiban pembayaran anak.') }}</p>
                    </div>
                    <a href="{{ route('wali.keuangan.dashboard', $siswa) }}" class="btn-nu-primary shrink-0">{{ __('Lihat laporan lengkap') }}</a>
                </div>

                @if ($keuangan['has_tunggakan'])
                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 sm:px-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full bg-amber-200/80 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-950">{{ __('Ada tunggakan') }}</span>
                            <span class="text-sm text-amber-950">
                                {{ __(':count tagihan belum lunas · Total sisa', ['count' => $keuangan['jumlah_belum_lunas']]) }}
                                <span class="font-bold">@include('keuangan.partials.rupiah', ['value' => $keuangan['total_sisa'], 'decimals' => 0])</span>
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                    <th class="py-2 pr-4">{{ __('Harus dibayar') }}</th>
                                    <th class="py-2 pr-4">{{ __('Periode') }}</th>
                                    <th class="py-2 pr-4 text-right">{{ __('Sisa') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($keuangan['harus_bayar']->take(5) as $row)
                                    @php $t = $row['tagihan']; @endphp
                                    <tr>
                                        <td class="py-2.5 pr-4 font-semibold text-gray-900">{{ $t->jenis }}</td>
                                        <td class="py-2.5 pr-4 font-mono text-gray-600">{{ $t->periode }}</td>
                                        <td class="py-2.5 pr-4 text-right font-mono font-bold text-amber-800">@include('keuangan.partials.rupiah', ['value' => $row['sisa']])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($keuangan['harus_bayar']->count() > 5)
                            <p class="mt-2 text-xs text-gray-500">{{ __('Dan :n tagihan lainnya…', ['n' => $keuangan['harus_bayar']->count() - 5]) }}</p>
                        @endif
                    </div>
                @else
                    <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 sm:px-5">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex rounded-full bg-emerald-200/80 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-900">{{ __('Tidak ada tunggakan') }}</span>
                            <span class="text-sm text-emerald-900">{{ __('Semua tagihan sudah lunas.') }}</span>
                        </div>
                    </div>
                @endif
            </section>

            <div class="grid gap-4 sm:grid-cols-3">
                <a href="{{ route('wali.keuangan.dashboard', $siswa) }}" class="block rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tagihan belum lunas') }}</div>
                    <div class="mt-2 text-3xl font-extrabold {{ $keuangan['has_tunggakan'] ? 'text-amber-700' : 'text-emerald-600' }}">{{ number_format($keuangan['jumlah_belum_lunas']) }}</div>
                    <div class="mt-3 text-sm font-bold text-nu-primary">{{ __('Dashboard keuangan') }} →</div>
                </a>
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Perizinan pending') }}</div>
                    <div class="mt-2 text-3xl font-extrabold text-nu-primary">{{ number_format((int) $izinPending) }}</div>
                </div>
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total pelanggaran (BK)') }}</div>
                    <div class="mt-2 text-3xl font-extrabold text-nu-primary">{{ number_format((int) $pelanggaranCount) }}</div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-12">
                <div class="lg:col-span-5 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-extrabold text-gray-900">{{ __('Presensi 7 hari terakhir') }}</div>
                    </div>
                    <div class="mt-4 space-y-2">
                        @forelse ($presensi7d as $p)
                            <div class="flex items-center justify-between rounded-2xl bg-gray-50 px-4 py-3 ring-1 ring-gray-100">
                                <div class="text-sm font-semibold text-gray-700">{{ optional($p->tanggal)->format('d M Y') }}</div>
                                <div class="text-xs font-extrabold uppercase tracking-wide text-gray-600">{{ $p->status }}</div>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-gray-50 p-6 text-center text-sm text-gray-500 ring-1 ring-gray-100">{{ __('Belum ada data presensi.') }}</div>
                        @endforelse
                    </div>
                </div>

                <div class="lg:col-span-7 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-extrabold text-gray-900">{{ __('Nilai terbaru') }}</div>
                    </div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                    <th class="py-2 pr-4">{{ __('Tanggal') }}</th>
                                    <th class="py-2 pr-4">{{ __('Mapel') }}</th>
                                    <th class="py-2 pr-4">{{ __('Jenis') }}</th>
                                    <th class="py-2 pr-4">{{ __('Nilai') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse ($nilaiLatest as $n)
                                    <tr class="text-sm text-gray-700">
                                        <td class="py-3 pr-4 whitespace-nowrap">{{ optional($n->tanggal)->format('d M Y') }}</td>
                                        <td class="py-3 pr-4 font-semibold text-gray-900">{{ $n->mataPelajaran->nama ?? '-' }}</td>
                                        <td class="py-3 pr-4 text-gray-600">{{ $n->jenis }}</td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700 ring-1 ring-emerald-100">{{ (int) $n->nilai }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-sm text-gray-500">{{ __('Belum ada nilai.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
