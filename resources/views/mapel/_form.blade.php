@props(['mapel' => null])

<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Kode') }}</label>
        <input
            name="kode"
            type="text"
            maxlength="16"
            value="{{ old('kode', $mapel?->kode) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="MTK / BINDO / IPA"
            required
        />
        @error('kode')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama mata pelajaran') }}</label>
        <input
            name="nama"
            type="text"
            value="{{ old('nama', $mapel?->nama) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Contoh: Matematika"
            required
        />
        @error('nama')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

