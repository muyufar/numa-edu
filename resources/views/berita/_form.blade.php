@php($b = $berita ?? null)
<div class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Judul') }}</label>
        <input type="text" name="judul" value="{{ old('judul', $b?->judul) }}" maxlength="255" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
        @error('judul')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Ringkasan') }}</label>
        <textarea name="ringkasan" rows="2" maxlength="2000" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Tampil di daftar publik (opsional)') }}">{{ old('ringkasan', $b?->ringkasan) }}</textarea>
        @error('ringkasan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Isi berita') }}</label>
        <textarea name="isi" rows="12" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>{{ old('isi', $b?->isi) }}</textarea>
        @error('isi')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/80 px-4 py-3">
        <input type="checkbox" name="is_published" value="1" class="h-4 w-4 rounded border-gray-300 text-nu-primary focus:ring-nu-primary" {{ old('is_published', $b?->is_published) ? 'checked' : '' }} />
        <label class="text-sm font-medium text-gray-800">{{ __('Terbitkan ke halaman publik') }}</label>
    </div>
</div>
