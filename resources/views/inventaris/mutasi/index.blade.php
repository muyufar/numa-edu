<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Inventaris · Mutasi') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Pencatatan barang masuk/keluar dan penyesuaian stok.') }}</p>
            </div>
            @can('create', \App\Models\InventarisMutasi::class)
                <a href="{{ route('inventaris.mutasi.create', ['barang_id' => $barangId]) }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Catat mutasi') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
            <form method="GET" action="{{ route('inventaris.mutasi.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 sm:items-end">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Barang') }}</label>
                    <select name="barang_id" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach ($barangOptions as $b)
                            <option value="{{ $b->id }}" {{ (string) $barangId === (string) $b->id ? 'selected' : '' }}>
                                {{ $b->nama }} {{ $b->kode ? '· '.$b->kode : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tipe') }}</label>
                    <select name="tipe" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach (\App\Models\InventarisMutasi::TIPE_OPTIONS as $t)
                            <option value="{{ $t }}" {{ (string) $tipe === (string) $t ? 'selected' : '' }}>{{ \App\Models\InventarisMutasi::tipeLabel($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Terapkan') }}</button>
                    <a href="{{ route('inventaris.mutasi.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $mutasis->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Tanggal') }}</th>
                            <th class="px-5 py-3">{{ __('Barang') }}</th>
                            <th class="px-5 py-3">{{ __('Tipe') }}</th>
                            <th class="px-5 py-3">{{ __('Sumber') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Jumlah') }}</th>
                            <th class="px-5 py-3">{{ __('Referensi') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($mutasis as $m)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ $m->tanggal?->format('Y-m-d') }}</td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $m->barang?->nama }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $m->barang?->kode ?: '—' }}</div>
                                </td>
                                <td class="px-5 py-3">{{ \App\Models\InventarisMutasi::tipeLabel($m->tipe) }}</td>
                                <td class="px-5 py-3 text-gray-700">
                                    @if ($m->tipe === 'in')
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-800 ring-1 ring-indigo-200">
                                            {{ \App\Models\InventarisMutasi::sumberPengadaanLabel($m->sumber_pengadaan) }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right font-mono text-xs text-gray-700">{{ $m->jumlah }}</td>
                                <td class="px-5 py-3 text-gray-700">
                                    <div>{{ $m->referensi ?: '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit((string) $m->keterangan, 50) }}</div>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @can('update', $m)
                                        <a href="{{ route('inventaris.mutasi.edit', $m) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada data.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($mutasis->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $mutasis->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

