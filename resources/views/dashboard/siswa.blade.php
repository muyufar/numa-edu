@php
    $jamDisplay = static fn ($v) => $v ? substr((string) $v, 0, 5) : '—';
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">{{ __('Dashboard siswa') }}</h1>
                <p class="mt-1 max-w-2xl text-sm text-gray-600">{{ __('Jadwal kelas, riwayat presensi, perizinan, dan perpustakaan digital.') }}</p>
            </div>
            @if ($siswa)
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 font-mono text-xs font-semibold text-gray-700 ring-1 ring-gray-200">{{ now()->format('Y-m-d') }}</span>
                    @if ($siswa->kelas)
                        <span class="inline-flex items-center rounded-full bg-nu-primary/10 px-3 py-1 text-xs font-semibold text-nu-primary ring-1 ring-nu-primary/15">
                            {{ __('Kelas') }} {{ $siswa->kelas->tingkat }} {{ $siswa->kelas->nama }} · {{ $siswa->kelas->tahun_ajaran }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        @unless ($siswa)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-950">
                {{ __('Akun siswa belum terhubung ke data siswa di sekolah. Hubungi admin untuk menghubungkan profil Anda.') }}
            </div>
        @else
            <x-dashboard.panel :title="__('Jadwal pelajaran')" :subtitle="$siswa->kelas ? __('Jadwal kelas :kelas', ['kelas' => $siswa->kelas->tingkat.' '.$siswa->kelas->nama]) : __('Belum ada kelas')" :badge="$siswa->kelas?->tahun_ajaran">
                @if (! $siswa->kelas)
                    <p class="text-sm text-gray-500">{{ __('Anda belum ditempatkan di kelas. Jadwal akan tampil setelah admin memasukkan Anda ke rombel.') }}</p>
                @elseif ($jadwals->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Belum ada jadwal untuk kelas Anda.') }}</p>
                @else
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Hari') }}</th>
                                    <th class="px-4 py-3">{{ __('Jam') }}</th>
                                    <th class="px-4 py-3">{{ __('Mapel') }}</th>
                                    <th class="px-4 py-3">{{ __('Guru') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($jadwals as $j)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $j->hari }}</td>
                                        <td class="px-4 py-3 font-mono text-gray-700">{{ $jamDisplay($j->jam_mulai) }}–{{ $jamDisplay($j->jam_selesai) }}</td>
                                        <td class="px-4 py-3 text-gray-800">
                                            <span class="font-medium">{{ $j->mataPelajaran?->nama ?? '—' }}</span>
                                            @if ($j->mataPelajaran?->kode)
                                                <span class="text-xs text-gray-500">({{ $j->mataPelajaran->kode }})</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">{{ $j->guru?->nama ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-dashboard.panel>

            <div class="grid gap-6 lg:grid-cols-2">
                <x-dashboard.panel :title="__('Riwayat presensi')" :subtitle="__('10 catatan terakhir')" :badge="__('Presensi')">
                    <x-slot:action>
                        @can('viewAny', \App\Models\PresensiSiswa::class)
                            <a href="{{ route('presensi.siswa.index') }}" class="text-xs font-semibold text-nu-primary hover:underline">{{ __('Lihat semua') }}</a>
                        @endcan
                    </x-slot:action>

                    @if ($presensiRows->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('Belum ada data presensi.') }}</p>
                    @else
                        <div class="divide-y divide-gray-100 rounded-xl border border-gray-100">
                            @foreach ($presensiRows as $p)
                                <div class="flex items-start justify-between gap-3 px-4 py-3">
                                    <div>
                                        <div class="font-mono text-sm font-semibold text-gray-900">{{ $p->tanggal?->format('Y-m-d') }}</div>
                                        <div class="mt-0.5 text-xs text-gray-500">
                                            @if ($perMapel)
                                                {{ $p->jadwal?->mataPelajaran?->nama ?? ($p->presensi_slot === 'harian' ? __('Harian') : '—') }}
                                            @else
                                                {{ __('Kehadiran harian') }}
                                            @endif
                                            @if ($p->jam_masuk)
                                                · {{ substr((string) $p->jam_masuk, 0, 5) }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800 ring-1 ring-gray-200">
                                        @include('presensi.partials.status-label', ['status' => $p->status])
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-dashboard.panel>

                <x-dashboard.panel :title="__('Perizinan diajukan')" :subtitle="__('Izin, sakit, dan dispensasi')" :badge="__('Perizinan')">
                    @if ($perizinanRows->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('Belum ada perizinan yang diajukan untuk Anda.') }}</p>
                    @else
                        <div class="divide-y divide-gray-100 rounded-xl border border-gray-100">
                            @foreach ($perizinanRows as $izin)
                                <div class="flex items-start justify-between gap-3 px-4 py-3">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ \App\Models\Perizinan::jenisLabel($izin->jenis) }}</div>
                                        <div class="mt-0.5 font-mono text-xs text-gray-500">{{ $izin->tanggal?->format('Y-m-d') }}</div>
                                        @if ($izin->keterangan)
                                            <div class="mt-1 text-xs text-gray-600">{{ \Illuminate\Support\Str::limit($izin->keterangan, 80) }}</div>
                                        @endif
                                    </div>
                                    <span @class([
                                        'inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold ring-1',
                                        'bg-amber-50 text-amber-800 ring-amber-200' => $izin->status === 'pending',
                                        'bg-emerald-50 text-emerald-800 ring-emerald-200' => $izin->status === 'approved',
                                        'bg-red-50 text-red-800 ring-red-200' => $izin->status === 'rejected',
                                        'bg-gray-50 text-gray-700 ring-gray-200' => ! in_array($izin->status, ['pending', 'approved', 'rejected'], true),
                                    ])>
                                        {{ \App\Models\Perizinan::statusLabel($izin->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-dashboard.panel>
            </div>

            @can('viewAny', \App\Models\PerpustakaanBuku::class)
                <x-dashboard.panel :title="__('Perpustakaan digital')" :subtitle="__('Pinjam dan baca e-book sekolah')" :badge="__('E-book')">
                    <x-slot:action>
                        <a href="{{ route('perpustakaan.buku.index', ['digital' => 1]) }}" class="text-xs font-semibold text-nu-primary hover:underline">{{ __('Katalog e-book') }}</a>
                    </x-slot:action>
                    <p class="text-sm text-gray-600">{{ __('Jelajahi koleksi e-book, pinjam buku digital, dan baca langsung dari portal siswa.') }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('perpustakaan.buku.index', ['digital' => 1]) }}" class="btn-nu-primary text-sm">{{ __('Lihat katalog') }}</a>
                        <a href="{{ route('perpustakaan.peminjaman.index', ['tab' => 'saya']) }}" class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-800 ring-1 ring-gray-200 hover:bg-gray-50">{{ __('Peminjaman saya') }}</a>
                    </div>
                </x-dashboard.panel>
            @endcan
        @endunless
    </div>
</x-app-layout>
