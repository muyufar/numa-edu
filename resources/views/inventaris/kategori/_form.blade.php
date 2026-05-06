<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Nama') }}</label>
        <input
            type="text"
            name="nama"
            value="{{ old('nama', $kategori->nama) }}"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
            maxlength="120"
        />
        @error('nama')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Deskripsi') }}</label>
        <textarea
            name="deskripsi"
            rows="4"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
        >{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
        @error('deskripsi')
            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>

