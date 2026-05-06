<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Daftar akun (COA)') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kode, nama, tipe akun untuk jurnal dan pelaporan.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('keuangan.index') }}" class="btn-nu">{{ __('Keuangan') }}</a>
                <a href="{{ route('akuntansi.index') }}" class="btn-nu">{{ __('Akuntansi') }}</a>
                @can('create', \App\Models\Tagihan::class)
                    <a href="{{ route('keuangan.coa.create') }}" class="btn-nu-primary">{{ __('Tambah akun') }}</a>
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
        @if ($errors->has('coa'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                {{ $errors->first('coa') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <form method="GET" action="{{ route('keuangan.coa.index') }}" class="grid gap-4 sm:grid-cols-12 sm:items-end">
                <div class="sm:col-span-4">
                    <x-input-label for="q" :value="__('Cari kode / nama')" />
                    <x-text-input id="q" name="q" class="mt-2 block w-full" type="search" :value="$q" />
                </div>
                <div class="sm:col-span-3">
                    <x-input-label for="tipe" :value="__('Tipe')" />
                    <select id="tipe" name="tipe" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                        <option value="">{{ __('Semua') }}</option>
                        @foreach (\App\Models\AkuntansiAkun::TIPE_OPTIONS as $t)
                            <option value="{{ $t }}" @selected($tipe === $t)>{{ $t }}</option>
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
                    <a href="{{ route('keuangan.coa.index') }}" class="btn-nu">{{ __('Reset') }}</a>
                    <x-primary-button type="submit">{{ __('Terapkan') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Kode') }}</th>
                            <th class="px-5 py-3">{{ __('Nama') }}</th>
                            <th class="px-5 py-3">{{ __('Tipe') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Baris jurnal') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($rows as $row)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-5 py-3 font-mono font-semibold text-gray-900">{{ $row->kode }}</td>
                                <td class="px-5 py-3 text-gray-800">{{ $row->nama }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $row->tipe }}</td>
                                <td class="px-5 py-3 text-right font-mono text-gray-700">{{ number_format($row->jurnal_lines_count) }}</td>
                                <td class="px-5 py-3">
                                    @if ($row->is_active)
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">{{ __('Nonaktif') }}</span>
                                    @endif
                                    @if ($row->isReservedSystemKode())
                                        <span class="ml-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-900">{{ __('Bawaan') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @can('create', \App\Models\Tagihan::class)
                                        <a href="{{ route('keuangan.coa.edit', $row) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Ubah') }}</a>
                                        @if (! $row->isReservedSystemKode() && $row->jurnal_lines_count === 0)
                                            <span class="mx-1 text-gray-300">|</span>
                                            <form method="POST" action="{{ route('keuangan.coa.destroy', $row) }}" class="inline" onsubmit="return confirm(@json(__('Hapus akun ini?')))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">{{ __('Hapus') }}</button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-600">{{ __('Belum ada akun. Jalankan transaksi pembayaran atau buka Tambah akun.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($rows->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $rows->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
