@props(['kelas' => null])

@php
    $isEdit = (bool) $kelas;
@endphp

<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tingkat') }}</label>
        <input
            name="tingkat"
            type="number"
            min="1"
            max="12"
            value="{{ old('tingkat', $kelas?->tingkat) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        />
        @error('tingkat')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama kelas') }}</label>
        <input
            name="nama"
            type="text"
            maxlength="64"
            value="{{ old('nama', $kelas?->nama) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Contoh: A / B / 1 / 2"
            required
        />
        @error('nama')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
        <input
            name="tahun_ajaran"
            type="text"
            maxlength="16"
            value="{{ old('tahun_ajaran', $kelas?->tahun_ajaran) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Contoh: 2025/2026"
            required
        />
        @error('tahun_ajaran')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3 pt-7">
        <input
            id="is_active"
            name="is_active"
            type="checkbox"
            value="1"
            class="h-5 w-5 rounded border-gray-300 text-nu-primary focus:ring-nu-primary/30"
            {{ old('is_active', $kelas?->is_active) ? 'checked' : '' }}
        />
        <label for="is_active" class="text-sm font-semibold text-gray-700">{{ __('Aktif') }}</label>
    </div>
</div>

