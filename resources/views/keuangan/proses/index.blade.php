<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Proses pembayaran') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Pilih siswa & periode, lalu catat pembayaran kewajiban yang belum lunas.') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
            <div class="text-sm font-bold text-gray-900">{{ __('Pilih siswa & periode') }}</div>
            <form method="GET" action="{{ route('keuangan.proses.index') }}" class="mt-4 grid gap-4 sm:grid-cols-12">
                @php
                    $selectBase = 'w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
                    $fieldWrap = 'space-y-2';
                @endphp

                <div class="sm:col-span-12 lg:col-span-6 {{ $fieldWrap }}">
                    <x-input-label for="siswa_id" :value="__('Siswa')" />
                    <select id="siswa_id" name="siswa_id" class="{{ $selectBase }}" required>
                        <option value="">{{ __('Pilih siswa...') }}</option>
                        @foreach ($siswaOptions as $s)
                            <option value="{{ $s->id }}" @selected((string) request('siswa_id') === (string) $s->id)>
                                {{ $s->nama }}@if($s->nis) ({{ $s->nis }})@endif
                                @if($s->kelas) · {{ $s->kelas->tingkat }} {{ $s->kelas->nama }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-6 lg:col-span-2 {{ $fieldWrap }}">
                    <x-input-label for="bulan" :value="__('Bulan')" />
                    <select id="bulan" name="bulan" class="{{ $selectBase }}" required>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected((int) request('bulan', $bulan) === $m)>{{ str_pad((string) $m, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>

                <div class="sm:col-span-6 lg:col-span-2 {{ $fieldWrap }}">
                    <x-input-label for="tahun" :value="__('Tahun')" />
                    <x-text-input id="tahun" name="tahun" class="block w-full font-mono" type="number" min="2000" max="2100" :value="request('tahun', $tahun)" required />
                    <div class="min-h-4 text-xs text-gray-500">{{ __('Filter memakai periode tagihan persis :format (harus sama dengan kolom periode di tagihan).', ['format' => 'YYYY-MM']) }}</div>
                </div>

                <div class="sm:col-span-12 lg:col-span-2 flex items-end">
                    <x-primary-button class="w-full justify-center">{{ __('Tampilkan') }}</x-primary-button>
                </div>
            </form>
        </div>

        @if ($selectedSiswa)
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 lg:col-span-2">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ __('Kewajiban yang belum lunas') }}</div>
                            <div class="mt-1 text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">{{ $selectedSiswa->nama }}</span>
                                <span class="text-gray-500">· {{ $periode }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('keuangan.proses.generate') }}">
                                @csrf
                                <input type="hidden" name="siswa_id" value="{{ $selectedSiswa->id }}">
                                <input type="hidden" name="bulan" value="{{ $bulan }}">
                                <input type="hidden" name="tahun" value="{{ $tahun }}">
                                <x-secondary-button>{{ __('Generate (siswa ini)') }}</x-secondary-button>
                            </form>

                            <details class="relative">
                                <summary class="cursor-pointer list-none">
                                    <x-secondary-button type="button">{{ __('Generate massal') }}</x-secondary-button>
                                </summary>
                                <div class="absolute right-0 z-10 mt-2 w-[24rem] rounded-2xl border border-gray-200 bg-white p-4 shadow-lg ring-1 ring-black/5">
                                    <div class="text-sm font-bold text-gray-900">{{ __('Generate massal') }}</div>
                                    <p class="mt-1 text-xs text-gray-500">{{ __('Tidak membuat duplikat jika sudah ada pada siswa+periode+jenis yang sama.') }}</p>

                                    <div class="mt-4 space-y-4">
                                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                                            <div class="text-xs font-bold uppercase tracking-wide text-gray-600">{{ __('Bulanan (dari master tipe bulanan)') }}</div>
                                            <form method="POST" action="{{ route('keuangan.proses.generate-mass') }}" class="mt-3 space-y-3">
                                                @csrf
                                                <input type="hidden" name="bulan" value="{{ $bulan }}">
                                                <input type="hidden" name="tahun" value="{{ $tahun }}">

                                                <div class="space-y-2">
                                                    <x-input-label for="kelas_id" :value="__('Target kelas (opsional)')" />
                                                    <select id="kelas_id" name="kelas_id" class="{{ $selectBase }}">
                                                        <option value="">{{ __('Semua siswa (di sekolah)') }}</option>
                                                        @foreach ($kelasOptions as $k)
                                                            <option value="{{ $k->id }}">{{ $k->tingkat }} {{ $k->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div class="space-y-2">
                                                        <x-input-label :value="__('Periode')" />
                                                        <div class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm text-gray-800">{{ $periode }}</div>
                                                    </div>
                                                    <div class="flex items-end">
                                                        <x-primary-button class="w-full justify-center">{{ __('Generate') }}</x-primary-button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                                            <div class="text-xs font-bold uppercase tracking-wide text-gray-600">{{ __('Insidental (pilih 1 kewajiban)') }}</div>
                                            <form method="POST" action="{{ route('keuangan.proses.generate-insidental') }}" class="mt-3 space-y-3">
                                                @csrf
                                                <input type="hidden" name="bulan" value="{{ $bulan }}">
                                                <input type="hidden" name="tahun" value="{{ $tahun }}">

                                                <div class="space-y-2">
                                                    <x-input-label for="kewajiban_id" :value="__('Kewajiban insidental')" />
                                                    <select id="kewajiban_id" name="kewajiban_id" class="{{ $selectBase }}" required>
                                                        <option value="">{{ __('Pilih kewajiban...') }}</option>
                                                        @foreach ($kewajibanInsidental as $k)
                                                            <option value="{{ $k->id }}">{{ $k->nama }} · Rp {{ number_format((float) $k->nominal_default, 0, ',', '.') }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="space-y-2">
                                                    <x-input-label for="kelas_id_insidental" :value="__('Target kelas (opsional)')" />
                                                    <select id="kelas_id_insidental" name="kelas_id" class="{{ $selectBase }}">
                                                        <option value="">{{ __('Semua siswa (di sekolah)') }}</option>
                                                        @foreach ($kelasOptions as $kls)
                                                            <option value="{{ $kls->id }}">{{ $kls->tingkat }} {{ $kls->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div class="space-y-2">
                                                        <x-input-label for="nominal_insidental" :value="__('Nominal (opsional)')" />
                                                        <x-text-input id="nominal_insidental" name="nominal" class="block w-full font-mono" type="number" step="0.01" min="0" placeholder="{{ __('default master') }}" />
                                                    </div>
                                                    <div class="space-y-2">
                                                        <x-input-label for="jatuh_tempo_insidental" :value="__('Jatuh tempo (opsional)')" />
                                                        <x-text-input id="jatuh_tempo_insidental" name="jatuh_tempo" class="block w-full font-mono" type="date" />
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div class="space-y-2">
                                                        <x-input-label :value="__('Periode')" />
                                                        <div class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm text-gray-800">{{ $periode }}</div>
                                                    </div>
                                                    <div class="flex items-end">
                                                        <x-primary-button class="w-full justify-center">{{ __('Buat tagihan') }}</x-primary-button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>

                    @php
                        $rows = $tagihans
                            ->map(function ($t) {
                                $dibayar = (float) ($t->total_dibayar ?? 0);
                                $sisa = max(0, (float) $t->jumlah - $dibayar);
                                return [
                                    'model' => $t,
                                    'dibayar' => $dibayar,
                                    'sisa' => $sisa,
                                ];
                            })
                            ->filter(fn ($r) => $r['sisa'] > 0.00001);
                        $totalSisa = (float) $rows->sum('sisa');
                    @endphp

                    @if ($rows->isEmpty())
                        @if ($tagihans->isNotEmpty())
                            <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                {{ __('Semua tagihan untuk periode :periode sudah lunas.', ['periode' => $periode]) }}
                            </div>
                        @elseif ($outstandingDiPeriodeLain->isNotEmpty())
                            <div class="mt-4 space-y-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                                <p class="font-semibold">{{ __('Tidak ada tagihan belum lunas untuk periode :periode, tetapi ada tagihan dengan teks periode lain.', ['periode' => $periode]) }}</p>
                                <p class="text-xs text-amber-900/90">{{ __('Proses pembayaran ini memfilter kolom periode tagihan secara persis (contoh 2026-04). Jika tagihan memakai format lain (mis. 2025/2026), ubah periode tagihan atau gunakan halaman detail tagihan.') }}</p>
                                <ul class="list-inside list-disc space-y-1 text-xs">
                                    @foreach ($outstandingDiPeriodeLain as $row)
                                        @php $tg = $row['tagihan']; @endphp
                                        <li>
                                            <span class="font-semibold">{{ $tg->jenis }}</span>
                                            <span class="font-mono text-amber-900/80">· {{ $tg->periode }}</span>
                                            <span class="font-mono">· {{ __('Sisa') }} Rp {{ number_format((float) $row['sisa'], 0, ',', '.') }}</span>
                                            <a href="{{ route('tagihan.edit', $tg) }}" class="ml-1 font-bold text-nu-primary hover:underline">{{ __('Buka') }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="mt-4 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                                {{ __('Belum ada tagihan untuk periode :periode. Gunakan tombol generate atau buat tagihan dari menu Tagihan.', ['periode' => $periode]) }}
                            </div>
                        @endif
                    @else
                        <form method="POST" action="{{ route('keuangan.proses.bayar') }}" class="mt-4 space-y-4">
                            @csrf
                            <input type="hidden" name="siswa_id" value="{{ $selectedSiswa->id }}">
                            <input type="hidden" name="bulan" value="{{ $bulan }}">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">

                            <div class="overflow-hidden rounded-2xl border border-gray-100">
                                <table class="min-w-full divide-y divide-gray-100 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="w-10 px-4 py-3 text-left">
                                                <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-nu-primary focus:ring-nu-primary/25"
                                                       x-data
                                                       x-on:click="
                                                           const checked = $event.target.checked;
                                                           document.querySelectorAll('input[name=&quot;tagihan_ids[]&quot;]').forEach(el => el.checked = checked);
                                                           document.querySelectorAll('input[data-amount-for]').forEach(el => el.disabled = !checked);
                                                       ">
                                            </th>
                                            <th class="px-4 py-3 text-left font-bold text-gray-700">{{ __('Kewajiban') }}</th>
                                            <th class="px-4 py-3 text-right font-bold text-gray-700">{{ __('Nominal') }}</th>
                                            <th class="px-4 py-3 text-right font-bold text-gray-700">{{ __('Terbayar') }}</th>
                                            <th class="px-4 py-3 text-right font-bold text-gray-700">{{ __('Sisa') }}</th>
                                            <th class="px-4 py-3 text-right font-bold text-gray-700">{{ __('Bayar') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach ($rows as $r)
                                            @php $t = $r['model']; @endphp
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <input type="checkbox"
                                                           name="tagihan_ids[]"
                                                           value="{{ $t->id }}"
                                                           class="h-4 w-4 rounded border-gray-300 text-nu-primary focus:ring-nu-primary/25"
                                                           checked
                                                           x-data
                                                           x-on:change="
                                                               const row = $el.closest('tr');
                                                               const input = row?.querySelector('input[data-amount-for]');
                                                               if (input) input.disabled = !$el.checked;
                                                           ">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="font-semibold text-gray-900">{{ $t->jenis }}</div>
                                                    <div class="mt-0.5 text-xs text-gray-500">
                                                        @if($t->jatuh_tempo) {{ __('Jatuh tempo') }}: <span class="font-mono">{{ \App\Support\DateTimeFormat::date($t->jatuh_tempo) }}</span> @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right font-mono text-gray-700">Rp {{ number_format((float) $t->jumlah, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-mono text-gray-700">Rp {{ number_format((float) $r['dibayar'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-mono font-bold text-amber-700">Rp {{ number_format((float) $r['sisa'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex justify-end">
                                                        <input
                                                            data-amount-for="{{ $t->id }}"
                                                            name="amounts[{{ $t->id }}]"
                                                            type="number"
                                                            min="0.01"
                                                            max="{{ (float) $r['sisa'] }}"
                                                            step="0.01"
                                                            value="{{ old('amounts.' . $t->id, (float) $r['sisa']) }}"
                                                            class="w-32 rounded-xl border border-gray-200 bg-white px-3 py-2 text-right font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
                                                            required
                                                        />
                                                    </div>
                                                    <x-input-error :messages="$errors->get('amounts.' . $t->id)" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td class="px-4 py-3" colspan="5">
                                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total harus dibayar') }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-base font-extrabold text-nu-primary">
                                                Rp {{ number_format((float) $totalSisa, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-12">
                                <div class="sm:col-span-6 lg:col-span-4 space-y-2">
                                    <x-input-label for="metode" :value="__('Metode bayar')" />
                                    <select id="metode" name="metode" class="{{ $selectBase }}" required>
                                        @foreach (\App\Models\Pembayaran::METODE_OPTIONS as $m)
                                            <option value="{{ $m }}" @selected(old('metode') === $m)>{{ ucfirst($m) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-6 lg:col-span-4 space-y-2">
                                    <x-input-label for="referensi" :value="__('No bukti / Referensi (opsional)')" />
                                    <x-text-input id="referensi" name="referensi" class="block w-full" type="text" :value="old('referensi')" placeholder="TRX-123 / Kuitansi-01" />
                                </div>
                                <div class="sm:col-span-6 lg:col-span-2 space-y-2">
                                    <x-input-label for="dibayar_pada" :value="__('Tanggal (opsional)')" />
                                    <x-text-input id="dibayar_pada" name="dibayar_pada" class="block w-full font-mono" type="date" :value="old('dibayar_pada')" />
                                </div>
                                <div class="sm:col-span-6 lg:col-span-2 flex items-end">
                                    <x-primary-button class="w-full justify-center">{{ __('Bayarkan terpilih') }}</x-primary-button>
                                </div>
                            </div>

                            <x-input-error :messages="$errors->get('tagihan_ids')" />
                            <x-input-error :messages="$errors->get('amounts')" />
                            <x-input-error :messages="$errors->get('metode')" />
                        </form>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-sm font-bold text-gray-900">{{ __('Ringkasan') }}</div>
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="text-gray-600">{{ __('Periode') }}</div>
                            <div class="font-mono font-semibold text-gray-900">{{ $periode }}</div>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="text-gray-600">{{ __('Master kewajiban (aktif)') }}</div>
                            <div class="font-semibold text-gray-900">{{ $kewajibanActive->count() }}</div>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="text-gray-600">{{ __('Total sisa di periode ini') }}</div>
                            <div class="font-mono font-extrabold text-nu-primary">Rp {{ number_format((float) $totalSisa, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-xs text-gray-600">
                        {{ __('Catatan: tombol "Generate tagihan bulanan" akan membuat tagihan berdasarkan Master Kewajiban (tipe bulanan) jika belum ada pada periode ini.') }}
                    </div>
                </div>
            </div>
        @else
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 lg:col-span-2">
                    <div class="text-sm text-gray-600">{{ __('Silakan pilih siswa untuk mulai memproses pembayaran.') }}</div>
                </div>
                <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-sm font-bold text-gray-900">{{ __('Generate massal') }}</div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Buat tagihan bulanan untuk periode yang dipilih (tanpa duplikat).') }}</p>

                    <form method="POST" action="{{ route('keuangan.proses.generate-mass') }}" class="mt-4 space-y-3">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">

                        <div class="space-y-2">
                            <x-input-label for="kelas_id_global" :value="__('Target kelas (opsional)')" />
                            <select id="kelas_id_global" name="kelas_id" class="{{ $selectBase }}">
                                <option value="">{{ __('Semua siswa (di sekolah)') }}</option>
                                @foreach ($kelasOptions as $k)
                                    <option value="{{ $k->id }}">{{ $k->tingkat }} {{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <x-input-label :value="__('Periode')" />
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 font-mono text-sm text-gray-800">{{ $periode }}</div>
                        </div>

                        <x-primary-button class="w-full justify-center">{{ __('Generate') }}</x-primary-button>
                    </form>

                    <div class="mt-5 border-t border-gray-100 pt-5">
                        <div class="text-sm font-bold text-gray-900">{{ __('Insidental massal') }}</div>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Buat 1 jenis tagihan insidental untuk target siswa (tanpa duplikat).') }}</p>

                        <form method="POST" action="{{ route('keuangan.proses.generate-insidental') }}" class="mt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="bulan" value="{{ $bulan }}">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">

                            <div class="space-y-2">
                                <x-input-label for="kewajiban_id_global" :value="__('Kewajiban insidental')" />
                                <select id="kewajiban_id_global" name="kewajiban_id" class="{{ $selectBase }}" required>
                                    <option value="">{{ __('Pilih kewajiban...') }}</option>
                                    @foreach ($kewajibanInsidental as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama }} · Rp {{ number_format((float) $k->nominal_default, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <x-input-label for="kelas_id_global_ins" :value="__('Target kelas (opsional)')" />
                                <select id="kelas_id_global_ins" name="kelas_id" class="{{ $selectBase }}">
                                    <option value="">{{ __('Semua siswa (di sekolah)') }}</option>
                                    @foreach ($kelasOptions as $k)
                                        <option value="{{ $k->id }}">{{ $k->tingkat }} {{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <x-input-label for="nominal_global_ins" :value="__('Nominal (opsional)')" />
                                    <x-text-input id="nominal_global_ins" name="nominal" class="block w-full font-mono" type="number" step="0.01" min="0" placeholder="{{ __('default master') }}" />
                                </div>
                                <div class="space-y-2">
                                    <x-input-label for="jatuh_tempo_global_ins" :value="__('Jatuh tempo (opsional)')" />
                                    <x-text-input id="jatuh_tempo_global_ins" name="jatuh_tempo" class="block w-full font-mono" type="date" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <x-input-label :value="__('Periode')" />
                                <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 font-mono text-sm text-gray-800">{{ $periode }}</div>
                            </div>

                            <x-primary-button class="w-full justify-center">{{ __('Buat tagihan') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

