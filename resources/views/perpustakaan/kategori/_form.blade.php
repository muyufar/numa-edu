@php $field = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20'; @endphp
<div class="space-y-4">
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Nama kategori') }}</label>
        <input name="nama" value="{{ old('nama', $kategori->nama) }}" class="{{ $field }}" required>
        @error('nama')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Kode') }}</label>
        <input name="kode" value="{{ old('kode', $kategori->kode) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Deskripsi') }}</label>
        <textarea name="deskripsi" rows="3" class="{{ $field }}">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
    </div>
</div>
