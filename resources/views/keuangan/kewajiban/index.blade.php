<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Master kewajiban pembayaran') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Template jenis kewajiban (bulanan/insidental) untuk pembuatan tagihan.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('keuangan.index') }}" class="btn-nu">{{ __('Keuangan') }}</a>
                <a href="{{ route('keuangan.kewajiban.create') }}" class="btn-nu-primary">{{ __('Tambah kewajiban') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <form method="GET" action="{{ route('keuangan.kewajiban.index') }}" class="grid gap-4 sm:grid-cols-12 sm:items-end">
                <div class="sm:col-span-6">
                    <x-input-label for="q" :value="__('Cari nama')" />
                    <x-text-input id="q" name="q" class="mt-2 block w-full" type="search" :value="$q" placeholder="mis. SPP / Uang Gedung" />
                </div>
                <div class="sm:col-span-3">
                    <x-input-label for="tipe" :value="__('Tipe')" />
                    <select id="tipe" name="tipe" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                        <option value="">{{ __('Semua') }}</option>
                        @foreach (\App\Models\KewajibanPembayaran::TIPE_OPTIONS as $t)
                            <option value="{{ $t }}" @selected($tipe === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <x-input-label for="active" :value="__('Status')" />
                    <select id="active" name="active" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                        <option value="">{{ __('Semua') }}</option>
                        <option value="1" @selected($active === '1')>{{ __('Aktif') }}</option>
                        <option value="0" @selected($active === '0')>{{ __('Nonaktif') }}</option>
                    </select>
                </div>
                <div class="sm:col-span-12 flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 pt-4">
                    <a href="{{ route('keuangan.kewajiban.index') }}" class="btn-nu">{{ __('Reset') }}</a>
                    <x-primary-button type="submit">{{ __('Terapkan') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Nama') }}</th>
                            <th class="px-5 py-3">{{ __('Tipe') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Nominal default') }}</th>
                            <th class="px-5 py-3">{{ __('Berlaku mulai') }}</th>
                            <th class="px-5 py-3">{{ __('Batas bayar') }}</th>
                            <th class="px-5 py-3">{{ __('Aktif') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($rows as $r)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-semibold text-gray-900">{{ $r->nama }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ ucfirst($r->tipe) }}</td>
                                <td class="px-5 py-3 text-right font-mono text-gray-900">@include('keuangan.partials.rupiah', ['value' => $r->nominal_default])</td>
                                <td class="px-5 py-3 font-mono text-gray-700">{{ $r->berlaku_mulai ?: '—' }}</td>
                                <td class="px-5 py-3 text-gray-700">
                                    @if ($r->batas_hari_bayar)
                                        {{ __('Tgl') }} {{ $r->batas_hari_bayar }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ($r->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200">{{ __('Nonaktif') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('keuangan.kewajiban.edit', $r) }}" class="btn-nu">{{ __('Edit') }}</a>
                                        <form method="POST" action="{{ route('keuangan.kewajiban.destroy', $r) }}" onsubmit="return confirm('{{ __('Hapus master kewajiban ini?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">{{ __('Hapus') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada master kewajiban.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $rows->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

