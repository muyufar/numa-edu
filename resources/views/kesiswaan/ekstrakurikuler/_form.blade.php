@php
    $r = $row ?? null;
@endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama') }}</label>
        <input type="text" name="nama" value="{{ old('nama', $r?->nama) }}" maxlength="160" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
        @error('nama')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Pembina / guru') }}</label>
            <select name="guru_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                <option value="">{{ __('— Opsional —') }}</option>
                @foreach ($guruOptions ?? [] as $g)
                    <option value="{{ $g->id }}" {{ (string) old('guru_id', $r?->guru_id) === (string) $g->id ? 'selected' : '' }}>
                        {{ $g->nama }}
                    </option>
                @endforeach
            </select>
            @error('guru_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Hari') }}</label>
            <input type="text" name="hari" value="{{ old('hari', $r?->hari) }}" maxlength="32" placeholder="{{ __('Mis. Senin, Rabu') }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('hari')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Jam') }}</label>
            <input type="text" name="jam" value="{{ old('jam', $r?->jam) }}" maxlength="32" placeholder="{{ __('Mis. 15:00–17:00') }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('jam')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Lokasi') }}</label>
            <input type="text" name="lokasi" value="{{ old('lokasi', $r?->lokasi) }}" maxlength="120" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('lokasi')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="flex cursor-pointer items-center gap-3">
            <input type="hidden" name="is_active" value="0" />
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="rounded border-gray-300 text-nu-primary focus:ring-nu-primary/25"
                @checked(old('is_active', $r?->is_active ?? true))
            />
            <span class="text-sm font-semibold text-gray-700">{{ __('Aktif') }}</span>
        </label>
        @error('is_active')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
