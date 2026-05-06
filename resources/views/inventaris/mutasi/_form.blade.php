<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Barang') }}</label>
        <select
            name="inventaris_barang_id"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        >
            <option value="">{{ __('— Pilih —') }}</option>
            @foreach ($barangOptions as $b)
                <option value="{{ $b->id }}" {{ (string) old('inventaris_barang_id', $mutasi->inventaris_barang_id) === (string) $b->id ? 'selected' : '' }}>
                    {{ $b->nama }} {{ $b->kode ? '· '.$b->kode : '' }}
                </option>
            @endforeach
        </select>
        @error('inventaris_barang_id')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tanggal') }}</label>
        <input
            type="date"
            name="tanggal"
            value="{{ old('tanggal', optional($mutasi->tanggal)->format('Y-m-d') ?? $mutasi->tanggal) }}"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        />
        @error('tanggal')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tipe') }}</label>
        <select
            name="tipe"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        >
            @foreach ($tipeOptions as $t)
                <option value="{{ $t }}" {{ (string) old('tipe', $mutasi->tipe) === (string) $t ? 'selected' : '' }}>{{ \App\Models\InventarisMutasi::tipeLabel($t) }}</option>
            @endforeach
        </select>
        @error('tipe')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Jumlah') }}</label>
        <input
            type="number"
            name="jumlah"
            value="{{ old('jumlah', $mutasi->jumlah) }}"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            min="1"
            required
        />
        @error('jumlah')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Referensi') }}</label>
        <input
            type="text"
            name="referensi"
            value="{{ old('referensi', $mutasi->referensi) }}"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            maxlength="120"
        />
        @error('referensi')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Keterangan') }}</label>
        <textarea
            name="keterangan"
            rows="4"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
        >{{ old('keterangan', $mutasi->keterangan) }}</textarea>
        @error('keterangan')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>

