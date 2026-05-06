@props([
    'overview' => [],
    'rekapPaginator' => null,
])

@php
    $cabangNama = $overview['cabang']?->nama;
    $scopeLabel = $cabangNama
        ? __('Wilayah: :nama', ['nama' => $cabangNama])
        : __('Ringkasan seluruh lembaga aktif');
    $chart = array_slice($overview['chart_kecamatan'] ?? [], 0, 14);
    $maxChart = max(1, collect($chart)->max(fn ($r) => max((int) ($r['siswa'] ?? 0), (int) ($r['guru'] ?? 0))) ?: 1);
    $w = 720;
    $h = 240;
    $padL = 36;
    $padR = 12;
    $padT = 12;
    $padB = 52;
    $plotW = $w - $padL - $padR;
    $plotH = $h - $padT - $padB;
    $nChart = max(1, count($chart));
    $akre = collect($overview['akreditasi'] ?? []);
    $akreTotal = $akre->sum('count');
    $deg = 0.0;
    $conicStops = [];
    foreach ($akre as $slice) {
        $cnt = (int) ($slice['count'] ?? 0);
        if ($cnt <= 0 || $akreTotal <= 0) {
            continue;
        }
        $span = ($cnt / $akreTotal) * 360;
        $d1 = $deg;
        $deg += $span;
        $conicStops[] = ($slice['color'] ?? '#94a3b8').' '.$d1.'deg '.$deg.'deg';
    }
    $conicCss = count($conicStops) > 0 ? 'conic-gradient('.implode(',', $conicStops).')' : '#e5e7eb';
    $sisPts = [];
    $guruPts = [];
    foreach ($chart as $i => $row) {
        $sx = $nChart <= 1 ? $padL + $plotW / 2 : $padL + ($plotW * ($i / ($nChart - 1)));
        $sv = (int) ($row['siswa'] ?? 0);
        $gv = (int) ($row['guru'] ?? 0);
        $sy = $padT + $plotH - ($sv / $maxChart) * $plotH;
        $gy = $padT + $plotH - ($gv / $maxChart) * $plotH;
        $sisPts[] = round($sx, 1).','.round($sy, 1);
        $guruPts[] = round($sx, 1).','.round($gy, 1);
    }
    $sisPoly = implode(' ', $sisPts);
    $guruPoly = implode(' ', $guruPts);
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-bold tracking-tight text-gray-900">{{ __('Ringkasan wilayah cabang') }}</h2>
            <p class="mt-0.5 text-xs text-gray-500">{{ $scopeLabel }}</p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-dashboard.stat-card
            :label="__('Total lembaga')"
            :value="number_format((int) ($overview['total_lembaga'] ?? 0), 0, ',', '.')"
            :hint="__('Jumlah lembaga aktif di wilayah ini')"
        />
        <x-dashboard.stat-card
            :label="__('Total siswa')"
            :value="number_format((int) ($overview['total_siswa'] ?? 0), 0, ',', '.')"
            :hint="__('Total semua siswa')"
        />
        <x-dashboard.stat-card
            :label="__('Total guru')"
            :value="number_format((int) ($overview['total_guru'] ?? 0), 0, ',', '.')"
            :hint="__('Jumlah guru di wilayah ini')"
        />
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard.stat-card
            :label="__('Siswa laki-laki')"
            :value="number_format((int) ($overview['siswa_l'] ?? 0), 0, ',', '.')"
        />
        <x-dashboard.stat-card
            :label="__('Siswa perempuan')"
            :value="number_format((int) ($overview['siswa_p'] ?? 0), 0, ',', '.')"
        />
        <x-dashboard.stat-card
            :label="__('Guru laki-laki')"
            :value="number_format((int) ($overview['guru_l'] ?? 0), 0, ',', '.')"
        />
        <x-dashboard.stat-card
            :label="__('Guru perempuan')"
            :value="number_format((int) ($overview['guru_p'] ?? 0), 0, ',', '.')"
        />
    </div>

    <div class="grid gap-4 lg:grid-cols-5">
        <x-dashboard.panel class="lg:col-span-3" :title="__('Siswa & guru per kecamatan')" :subtitle="__('Berdasarkan nama kecamatan pada profil lembaga')">
            @if (count($chart) === 0)
                <p class="text-sm text-gray-500">{{ __('Belum ada data kecamatan atau belum ada siswa/guru.') }}</p>
            @else
                <div class="overflow-x-auto rounded-2xl border border-gray-100 bg-gray-50/70 p-3">
                    <svg viewBox="0 0 {{ $w }} {{ $h }}" class="min-w-[560px] w-full" preserveAspectRatio="xMidYMid meet">
                        <line x1="{{ $padL }}" y1="{{ $padT + $plotH }}" x2="{{ $padL + $plotW }}" y2="{{ $padT + $plotH }}" stroke="#e5e7eb" stroke-width="1.5" />
                        @foreach ([0.25, 0.5, 0.75, 1.0] as $g)
                            @php $gy = $padT + $plotH - ($g * $plotH); @endphp
                            <line x1="{{ $padL }}" y1="{{ $gy }}" x2="{{ $padL + $plotW }}" y2="{{ $gy }}" stroke="#f3f4f6" stroke-width="1" stroke-dasharray="4 4" />
                        @endforeach
                        @if (count($sisPts) > 0)
                            <polyline fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" points="{{ $sisPoly }}" />
                            <polyline fill="none" stroke="#db2777" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" points="{{ $guruPoly }}" />
                            @foreach ($chart as $i => $row)
                                @php
                                    $sx = $nChart <= 1 ? $padL + $plotW / 2 : $padL + ($plotW * ($i / ($nChart - 1)));
                                    $sv = (int) ($row['siswa'] ?? 0);
                                    $gv = (int) ($row['guru'] ?? 0);
                                    $sy = $padT + $plotH - ($sv / $maxChart) * $plotH;
                                    $gy = $padT + $plotH - ($gv / $maxChart) * $plotH;
                                    $label = \Illuminate\Support\Str::limit((string) ($row['kecamatan'] ?? ''), 12);
                                @endphp
                                <circle cx="{{ $sx }}" cy="{{ $sy }}" r="4" fill="#2563eb" />
                                <circle cx="{{ $sx }}" cy="{{ $gy }}" r="4" fill="#db2777" />
                                <text x="{{ $sx }}" y="{{ $h - 10 }}" text-anchor="middle" font-size="10" fill="#6b7280" font-weight="600" transform="rotate(-32 {{ $sx }} {{ $h - 10 }})">{{ $label }}</text>
                            @endforeach
                        @endif
                    </svg>
                    <div class="mt-2 flex flex-wrap gap-4 text-xs font-semibold">
                        <span class="inline-flex items-center gap-2 text-blue-700"><span class="h-2 w-6 rounded bg-blue-600"></span> {{ __('Siswa') }}</span>
                        <span class="inline-flex items-center gap-2 text-pink-700"><span class="h-2 w-6 rounded bg-pink-600"></span> {{ __('Guru') }}</span>
                    </div>
                </div>
            @endif
        </x-dashboard.panel>

        <x-dashboard.panel class="lg:col-span-2" :title="__('Akreditasi lembaga')">
            @if ($akre->isEmpty())
                <p class="text-sm text-gray-500">{{ __('Belum ada data akreditasi.') }}</p>
            @else
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-start sm:justify-center">
                    <div
                        class="h-36 w-36 shrink-0 rounded-full ring-4 ring-white shadow-inner"
                        style="background: {{ $conicCss }};"
                        role="img"
                        aria-label="{{ __('Diagram akreditasi') }}"
                    ></div>
                    <ul class="w-full max-w-xs space-y-2 text-sm">
                        @foreach ($akre as $slice)
                            <li class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $slice['color'] }}"></span>
                                    <span class="font-medium text-gray-800">{{ $slice['label'] }}</span>
                                </span>
                                <span class="tabular-nums text-gray-600">{{ number_format((int) $slice['count'], 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-dashboard.panel>
    </div>

    <x-dashboard.panel :title="__('Rekap lembaga per kecamatan')" :subtitle="__('Kelurahan/desa yang tercatat di profil lembaga')">
        @if (! $rekapPaginator || $rekapPaginator->total() === 0)
            <p class="text-sm text-gray-500">{{ __('Belum ada lembaga dengan kecamatan terisi.') }}</p>
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Kecamatan') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Jml lembaga') }}</th>
                            <th class="px-4 py-3">{{ __('Desa asal lembaga') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($rekapPaginator as $r)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $r['kecamatan'] }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-gray-700">{{ number_format((int) $r['jml_lembaga'], 0, ',', '.') }}</td>
                                <td class="max-w-md px-4 py-3 text-gray-600">{{ $r['desa'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-gray-500">
                    {{ __('Menampilkan :from–:to dari :total', [
                        'from' => $rekapPaginator->firstItem() ?? 0,
                        'to' => $rekapPaginator->lastItem() ?? 0,
                        'total' => $rekapPaginator->total(),
                    ]) }}
                </p>
                {{ $rekapPaginator->onEachSide(1)->links() }}
            </div>
        @endif
    </x-dashboard.panel>
</div>
