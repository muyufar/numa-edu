<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Pelaporan') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Ekspor data ringkas untuk analisis di luar aplikasi.') }}</p>
        </div>
    </x-slot>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
            <h3 class="font-bold text-gray-900">{{ __('Data siswa (CSV)') }}</h3>
            <p class="mt-2 text-sm text-gray-600">{{ __('NIS, nama, kelas, tanggal lahir, jenis kelamin, alamat.') }}</p>
            <a href="{{ route('laporan.siswa-csv') }}" class="mt-4 inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                {{ __('Unduh CSV') }}
            </a>
        </div>
        <div class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
            <h3 class="font-bold text-gray-900">{{ __('Nilai (CSV)') }}</h3>
            <p class="mt-2 text-sm text-gray-600">{{ __('Tahun ajaran, semester, kelas, siswa, mapel, nilai akhir.') }}</p>
            <a href="{{ route('laporan.nilai-csv') }}" class="mt-4 inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                {{ __('Unduh CSV') }}
            </a>
        </div>
        <div class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 sm:col-span-2 lg:col-span-1">
            <h3 class="font-bold text-gray-900">{{ __('Presensi siswa (CSV)') }}</h3>
            <p class="mt-2 text-sm text-gray-600">{{ __('Tanggal, siswa, kelas, status, keterangan (seluruh riwayat).') }}</p>
            <a href="{{ route('laporan.presensi-siswa-csv') }}" class="mt-4 inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                {{ __('Unduh CSV') }}
            </a>
        </div>
        <div class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 sm:col-span-2 lg:col-span-3">
            <h3 class="font-bold text-gray-900">{{ __('Kurikulum (CSV)') }}</h3>
            <p class="mt-2 text-sm text-gray-600">{{ __('Tahun ajaran, semester, tingkat, mapel, jam per minggu, urutan, status aktif, catatan.') }}</p>
            <a href="{{ route('laporan.kurikulum-csv') }}" class="mt-4 inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                {{ __('Unduh CSV') }}
            </a>
        </div>
        @can('viewAny', \App\Models\Tagihan::class)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5 sm:col-span-2 lg:col-span-3">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('Keuangan (CSV)') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('Ekspor tagihan & pembayaran dengan filter periode/kategori untuk rekap bulanan.') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('laporan.tagihan-csv', request()->query()) }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                            {{ __('Unduh Tagihan (CSV)') }}
                        </a>
                        <a href="{{ route('laporan.pembayaran-csv', request()->query()) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-50">
                            {{ __('Unduh Pembayaran (CSV)') }}
                        </a>
                        <a href="{{ route('keuangan.tunggakan.export', array_filter([
                            'periode_from' => request('periode_from'),
                            'periode_to' => request('periode_to'),
                            'kelas_id' => request('kelas_id'),
                        ], fn ($v) => $v !== null && $v !== '')) }}" class="inline-flex items-center justify-center rounded-xl border border-orange-200 bg-orange-50/80 px-4 py-2.5 text-sm font-semibold text-orange-900 shadow-sm hover:bg-orange-100">
                            {{ __('Unduh Tunggakan (CSV)') }}
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('laporan.index') }}" class="mt-5 grid gap-4 sm:grid-cols-12">
                    @php
                        $selectBase = 'w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
                        $fieldWrap = 'space-y-2';
                    @endphp

                    <div class="sm:col-span-6 lg:col-span-2 {{ $fieldWrap }}">
                        <label class="text-xs font-semibold text-gray-600">{{ __('Periode dari') }}</label>
                        <input name="periode_from" value="{{ request('periode_from') }}" placeholder="2026-01" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
                    </div>
                    <div class="sm:col-span-6 lg:col-span-2 {{ $fieldWrap }}">
                        <label class="text-xs font-semibold text-gray-600">{{ __('Periode sampai') }}</label>
                        <input name="periode_to" value="{{ request('periode_to') }}" placeholder="2026-12" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
                    </div>

                    <div class="sm:col-span-6 lg:col-span-3 {{ $fieldWrap }}">
                        <label class="text-xs font-semibold text-gray-600">{{ __('Kelas (opsional)') }}</label>
                        <select name="kelas_id" class="{{ $selectBase }}">
                            <option value="">{{ __('Semua kelas') }}</option>
                            @foreach (($kelasOptions ?? collect()) as $k)
                                <option value="{{ $k->id }}" @selected((string) request('kelas_id') === (string) $k->id)>
                                    {{ $k->tingkat }} {{ $k->nama }} {{ $k->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-6 lg:col-span-2 {{ $fieldWrap }}">
                        <label class="text-xs font-semibold text-gray-600">{{ __('Status tagihan') }}</label>
                        <select name="status" class="{{ $selectBase }}">
                            <option value="">{{ __('Semua') }}</option>
                            @foreach (['unpaid' => 'unpaid', 'partial' => 'partial', 'paid' => 'paid'] as $v => $lbl)
                                <option value="{{ $v }}" @selected(request('status') === $v)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-6 lg:col-span-2 {{ $fieldWrap }}">
                        <label class="text-xs font-semibold text-gray-600">{{ __('Metode bayar') }}</label>
                        <select name="metode" class="{{ $selectBase }}">
                            <option value="">{{ __('Semua') }}</option>
                            @foreach (\App\Models\Pembayaran::METODE_OPTIONS as $m)
                                <option value="{{ $m }}" @selected(request('metode') === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-12 lg:col-span-1 flex items-end">
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-50">
                            {{ __('Terapkan') }}
                        </button>
                    </div>
                </form>
            </div>
        @endcan
    </div>
</x-app-layout>
