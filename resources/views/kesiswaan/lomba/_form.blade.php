@php
    $r = $row ?? null;
    $tanggalMulaiDefault = $r?->tanggal_mulai?->format('Y-m-d') ?? '';
    $tanggalSelesaiDefault = $r?->tanggal_selesai?->format('Y-m-d') ?? '';
@endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama') }}</label>
        <input type="text" name="nama" value="{{ old('nama', $r?->nama) }}" maxlength="160" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
        @error('nama')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tingkat') }}</label>
            <input type="text" name="tingkat" value="{{ old('tingkat', $r?->tingkat) }}" maxlength="64" placeholder="{{ __('Mis. kabupaten, provinsi') }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('tingkat')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal mulai') }}</label>
            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $tanggalMulaiDefault) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('tanggal_mulai')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal selesai') }}</label>
            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $tanggalSelesaiDefault) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('tanggal_selesai')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Lokasi') }}</label>
            <input type="text" name="lokasi" value="{{ old('lokasi', $r?->lokasi) }}" maxlength="160" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('lokasi')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Penyelenggara') }}</label>
            <input type="text" name="penyelenggara" value="{{ old('penyelenggara', $r?->penyelenggara) }}" maxlength="160" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('penyelenggara')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Keterangan') }}</label>
        <textarea name="keterangan" rows="3" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional') }}">{{ old('keterangan', $r?->keterangan) }}</textarea>
        @error('keterangan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
