@props(['pegawai' => null])

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama lengkap') }}</label>
        <input type="text" name="nama" value="{{ old('nama', $pegawai?->nama) }}" maxlength="255" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
        @error('nama')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('NIP / ID') }}</label>
        <input type="text" name="nip" value="{{ old('nip', $pegawai?->nip) }}" maxlength="32" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional') }}" />
        @error('nip')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Jabatan') }}</label>
        <input type="text" name="jabatan" value="{{ old('jabatan', $pegawai?->jabatan) }}" maxlength="128" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional') }}" />
        @error('jabatan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="sm:col-span-2 flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/80 px-4 py-3">
        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-nu-primary focus:ring-nu-primary" {{ old('is_active', $pegawai?->is_active ?? true) ? 'checked' : '' }} />
        <label class="text-sm font-medium text-gray-800">{{ __('Aktif (ikut presensi)') }}</label>
    </div>
</div>
