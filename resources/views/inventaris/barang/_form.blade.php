<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Nama') }}</label>
        <input
            type="text"
            name="nama"
            value="{{ old('nama', $barang->nama) }}"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
            maxlength="160"
        />
        @error('nama')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kode') }}</label>
        <input
            type="text"
            name="kode"
            value="{{ old('kode', $barang->kode) }}"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            maxlength="64"
        />
        @error('kode')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <div class="flex items-end justify-between gap-2">
            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kategori') }}</label>
            @can('create', \App\Models\InventarisKategori::class)
                <a href="{{ route('inventaris.kategori.create') }}" class="text-xs font-semibold text-nu-primary hover:underline">
                    {{ __('Tambah kategori') }}
                </a>
            @endcan
        </div>
        <select
            name="inventaris_kategori_id"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
        >
            <option value="">{{ __('— Tidak ada —') }}</option>
            @foreach ($kategoriOptions as $k)
                <option value="{{ $k->id }}" {{ (string) old('inventaris_kategori_id', $barang->inventaris_kategori_id) === (string) $k->id ? 'selected' : '' }}>
                    {{ $k->nama }}
                </option>
            @endforeach
        </select>
        @error('inventaris_kategori_id')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Satuan') }}</label>
        <input
            type="text"
            name="satuan"
            value="{{ old('satuan', $barang->satuan) }}"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
            maxlength="32"
        />
        @error('satuan')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Stok awal') }}</label>
        <input
            type="number"
            name="stok_awal"
            value="{{ old('stok_awal', $barang->stok_awal) }}"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            min="0"
            required
        />
        @error('stok_awal')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Stok minimum') }}</label>
        <input
            type="number"
            name="stok_minimum"
            value="{{ old('stok_minimum', $barang->stok_minimum) }}"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            min="0"
            required
        />
        @error('stok_minimum')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-nu-primary focus:ring-nu-primary/20" {{ old('is_active', $barang->is_active) ? 'checked' : '' }}>
            <span>{{ __('Aktif') }}</span>
        </label>
        @error('is_active')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Catatan') }}</label>
        <textarea
            name="catatan"
            rows="4"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
        >{{ old('catatan', $barang->catatan) }}</textarea>
        @error('catatan')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>

