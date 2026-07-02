<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Detail tagihan') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $tagihan->siswa?->nama }} · {{ $tagihan->jenis }} · {{ $tagihan->periode }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('tagihan.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Daftar') }}
                </a>
                @can('view', $tagihan)
                    <a href="{{ route('tagihan.invoice.pdf', $tagihan) }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                        {{ __('Invoice PDF') }}
                    </a>
                @endcan
                @can('update', $tagihan)
                    <a href="{{ route('tagihan.edit', $tagihan) }}" class="inline-flex items-center rounded-xl border border-nu-primary/30 bg-white px-4 py-2.5 text-sm font-semibold text-nu-primary shadow-sm hover:bg-nu-primary/5">
                        {{ __('Edit tagihan') }}
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 lg:col-span-1">
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

            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 lg:col-span-2">
                <div class="text-sm font-bold text-gray-900">{{ __('Riwayat pembayaran') }}</div>
                <div class="mt-3 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">{{ __('Tanggal') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Jumlah') }}</th>
                                <th class="px-4 py-3">{{ __('Metode') }}</th>
                                <th class="px-4 py-3">{{ __('Ref.') }}</th>
                                <th class="px-4 py-3">{{ __('Dicatat') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($tagihan->pembayarans as $p)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-mono text-gray-800">{{ \App\Support\DateTimeFormat::datetime($p->dibayar_pada) }}</td>
                                    <td class="px-4 py-3 text-right font-mono font-medium text-gray-900">@include('keuangan.partials.rupiah', ['value' => $p->jumlah])</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $p->metode }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $p->referensi ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $p->dicatatOleh?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        @can('view', $p)
                                            <a href="{{ route('pembayaran.kwitansi.pdf', $p) }}" class="text-xs font-semibold text-nu-primary hover:underline">{{ __('Kwitansi PDF') }}</a>
                                        @endcan
                                        @can('delete', $p)
                                            @can('view', $p)
                                                <span class="mx-1 text-gray-300">·</span>
                                            @endcan
                                            <form method="POST" action="{{ route('pembayaran.destroy', $p) }}" class="inline" onsubmit="return confirm('{{ __('Hapus pembayaran ini? Status tagihan akan dihitung ulang.') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">{{ __('Hapus') }}</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('Belum ada pembayaran.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @can('update', $tagihan)
                    @if ($sisa > 0.0001)
                        <div class="mt-6 border-t border-gray-100 pt-6">
                            <div class="text-sm font-bold text-gray-900">{{ __('Catat pembayaran') }}</div>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Maksimal') }}: @include('keuangan.partials.rupiah', ['value' => $sisa])</p>
                            <form method="POST" action="{{ route('tagihan.pembayaran.store', $tagihan) }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600">{{ __('Jumlah') }}</label>
                                    <input type="number" name="jumlah" step="0.01" min="0.01" max="{{ $sisa }}" value="{{ old('jumlah') }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2.5 font-mono text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
                                    @error('jumlah')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600">{{ __('Metode') }}</label>
                                    <select name="metode" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                                        @foreach (\App\Models\Pembayaran::METODE_OPTIONS as $m)
                                            <option value="{{ $m }}" {{ old('metode') === $m ? 'selected' : '' }}>
                                                {{ match ($m) {
                                                    'tunai' => __('Tunai'),
                                                    'transfer' => __('Transfer bank'),
                                                    'virtual' => __('Virtual account'),
                                                    'lainnya' => __('Lainnya'),
                                                    default => $m,
                                                } }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('metode')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600">{{ __('Referensi / no. bukti') }} <span class="font-normal">({{ __('opsional') }})</span></label>
                                    <input type="text" name="referensi" value="{{ old('referensi') }}" maxlength="255" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600">{{ __('Tanggal bayar') }} <span class="font-normal">({{ __('opsional') }})</span></label>
                                    <input type="datetime-local" name="dibayar_pada" value="{{ old('dibayar_pada') }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2.5 font-mono text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
                                    @error('dibayar_pada')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex items-end sm:col-span-2">
                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light sm:w-auto">
                                        {{ __('Simpan pembayaran') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                            {{ __('Tagihan sudah lunas.') }}
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
