<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">{{ __('Ringkasan sekolah') }}</h1>
                <p class="mt-1 max-w-2xl text-sm text-gray-600">{{ __('Lihat ringkasan cepat, pantau operasional, dan akses modul penting dari sini.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 font-mono text-xs font-semibold text-gray-700 ring-1 ring-gray-200">{{ now()->format('Y-m-d') }}</span>
                @if(auth()->user()->roles->isNotEmpty())
                    <span class="inline-flex items-center rounded-full bg-nu-primary/10 px-3 py-1 text-xs font-semibold text-nu-primary ring-1 ring-nu-primary/15">
                        {{ auth()->user()->roles->pluck('name')->join(' · ') }}
                    </span>
                @endif
            </div>
        </div>

        @if (! empty($pengurusOverview))
            <x-dashboard.pengurus-cabang-overview
                :overview="$pengurusOverview"
                :rekap-paginator="$pengurusRekapPaginator"
            />
            <div class="border-t border-gray-100 pt-2"></div>
        @endif

        @if (! empty($stats))
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $st)
                    <x-dashboard.stat-card :label="$st['label']" :value="$st['value']" :hint="$st['hint'] ?? null" />
                @endforeach
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                @if (! empty($siswa7d))
                    @php
                        $max = max(array_map(fn ($p) => (int) $p['count'], $siswa7d)) ?: 1;
                        $w = 560;
                        $h = 180;
                        $padX = 18;
                        $padY = 14;
                        $barGap = 10;
                        $bars = count($siswa7d);
                        $barW = (int) floor(($w - ($padX * 2) - ($barGap * ($bars - 1))) / max(1, $bars));
                        $baselineY = $h - 36;
                        $chartH = $baselineY - $padY;
                    @endphp
                    <x-dashboard.panel
                        :title="__('Grafik')"
                        :subtitle="__('Penerimaan siswa (7 hari terakhir)')"
                        :badge="__('7 hari')"
                    >
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-4">
                            <svg viewBox="0 0 {{ $w }} {{ $h }}" class="h-44 w-full">
                                <line x1="{{ $padX }}" y1="{{ $baselineY }}" x2="{{ $w - $padX }}" y2="{{ $baselineY }}" stroke="#e5e7eb" stroke-width="2" />
                                @foreach ($siswa7d as $i => $p)
                                    @php
                                        $count = (int) $p['count'];
                                        $ratio = $max > 0 ? ($count / $max) : 0;
                                        $barH = (int) round($ratio * $chartH);
                                        $minH = 6;
                                        $drawH = $count > 0 ? max($minH, $barH) : 2;
                                        $x = $padX + ($i * ($barW + $barGap));
                                        $y = $baselineY - $drawH;
                                        $label = \Illuminate\Support\Carbon::parse($p['date'])->format('d');
                                        $fill = $count > 0 ? '#0d4a2c' : '#cbd5e1';
                                    @endphp
                                    <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barW }}" height="{{ $drawH }}" rx="10" fill="{{ $fill }}" opacity="0.9">
                                        <title>{{ \Illuminate\Support\Carbon::parse($p['date'])->format('Y-m-d') }}: {{ $count }}</title>
                                    </rect>
                                    <text x="{{ $x + (int) floor($barW / 2) }}" y="{{ $baselineY + 18 }}" text-anchor="middle" font-size="12" fill="#6b7280" font-weight="600">
                                        {{ $label }}
                                    </text>
                                    <text x="{{ $x + (int) floor($barW / 2) }}" y="{{ $baselineY + 34 }}" text-anchor="middle" font-size="12" fill="#374151" font-weight="700">
                                        {{ $count }}
                                    </text>
                                @endforeach
                            </svg>
                        </div>
                    </x-dashboard.panel>
                @endif

                @if ($presensiToday)
                    <x-dashboard.panel :title="__('Presensi hari ini')" :badge="now()->format('Y-m-d')">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Siswa') }}</div>
                                <div class="mt-1 font-mono text-xl font-bold text-gray-900 tabular-nums">{{ $presensiToday['siswa'] }}</div>
                            </div>
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Guru') }}</div>
                                <div class="mt-1 font-mono text-xl font-bold text-gray-900 tabular-nums">{{ $presensiToday['guru'] }}</div>
                            </div>
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Pegawai') }}</div>
                                <div class="mt-1 font-mono text-xl font-bold text-gray-900 tabular-nums">{{ $presensiToday['pegawai'] }}</div>
                            </div>
                        </div>
                    </x-dashboard.panel>
                @endif

                <x-dashboard.panel :title="__('Aktivitas terbaru')" :badge="__('Ringkas')">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-gray-100">
                            <div class="border-b border-gray-100 px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Perizinan') }}</div>
                            <div class="divide-y divide-gray-100">
                                @forelse ($recentPerizinan as $p)
                                    <a href="{{ route('perizinan.edit', $p) }}" class="block px-4 py-3 hover:bg-gray-50/80">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $p->siswa?->nama }}</div>
                                                <div class="mt-0.5 text-xs text-gray-500">{{ \App\Models\Perizinan::jenisLabel($p->jenis) }} · {{ $p->tanggal?->format('Y-m-d') }}</div>
                                            </div>
                                            <div class="text-xs font-semibold text-gray-700">{{ \App\Models\Perizinan::statusLabel($p->status) }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-4 py-6 text-center text-sm text-gray-500">{{ __('Belum ada data.') }}</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-100">
                            <div class="border-b border-gray-100 px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Mutasi inventaris') }}</div>
                            <div class="divide-y divide-gray-100">
                                @forelse ($recentMutasi as $m)
                                    <a href="{{ route('inventaris.mutasi.edit', $m) }}" class="block px-4 py-3 hover:bg-gray-50/80">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $m->barang?->nama }}</div>
                                                <div class="mt-0.5 text-xs text-gray-500">{{ \App\Models\InventarisMutasi::tipeLabel($m->tipe) }} · {{ $m->tanggal?->format('Y-m-d') }}</div>
                                            </div>
                                            <div class="text-xs font-semibold text-gray-700">{{ $m->jumlah }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-4 py-6 text-center text-sm text-gray-500">{{ __('Belum ada data.') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </x-dashboard.panel>

                @if ($recentUsers->isNotEmpty())
                    <x-dashboard.panel :title="__('Akun terbaru')" :badge="__('Terbaru')">
                        <div class="overflow-hidden rounded-xl border border-gray-100">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <tr>
                                        <th class="px-4 py-3">{{ __('Nama') }}</th>
                                        <th class="px-4 py-3 hidden sm:table-cell">{{ __('Email') }}</th>
                                        <th class="px-4 py-3 text-right">{{ __('Bergabung') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach($recentUsers as $u)
                                        <tr class="hover:bg-gray-50/80">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $u->name }}</td>
                                            <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $u->email }}</td>
                                            <td class="px-4 py-3 text-right text-gray-500">{{ $u->created_at?->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-dashboard.panel>
                @endif
            </div>

            <div class="space-y-4">
                <x-dashboard.panel :title="__('Modul cepat')" :badge="__('Menu')">
                    @if(empty($modules))
                        <p class="mt-3 text-sm text-gray-600">{{ __('Akun ini belum memiliki modul yang ditampilkan.') }}</p>
                    @else
                        <ul class="mt-4 space-y-3">
                            @foreach($modules as $mod)
                                <li class="rounded-xl border border-gray-100 bg-gray-50/60 p-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900">{{ $mod['title'] }}</p>
                                            <p class="mt-1 text-xs text-gray-600">{{ $mod['description'] }}</p>
                                        </div>
                                        <span class="shrink-0 rounded-lg bg-white px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-nu-primary ring-1 ring-gray-200">
                                            {{ __('Cepat') }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-dashboard.panel>

                @if ($recentPpdb->isNotEmpty())
                    <x-dashboard.panel :title="__('PPDB terbaru')" :badge="__('6 terakhir')">
                        <div class="divide-y divide-gray-100 rounded-xl border border-gray-100">
                            @foreach ($recentPpdb as $r)
                                <a href="{{ route('ppdb.show', $r) }}" class="block px-4 py-3 hover:bg-gray-50/80">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $r->nama }}</div>
                                            <div class="mt-0.5 text-xs text-gray-500">{{ $r->asal_sekolah ?: '—' }}</div>
                                        </div>
                                        <div class="text-xs font-semibold text-gray-700">{{ strtoupper($r->status) }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </x-dashboard.panel>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
