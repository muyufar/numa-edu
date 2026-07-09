@php
    $inputClass = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama sanksi') }}</label>
        <input type="text" name="nama" value="{{ old('nama', $row->nama) }}" class="{{ $inputClass }}" required maxlength="120" />
        @error('nama')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tingkat') }}</label>
        <select name="tingkat" class="{{ $inputClass }}" required>
            @foreach (\App\Support\BkTingkat::OPTIONS as $t)
                <option value="{{ $t }}" {{ old('tingkat', $row->tingkat) === $t ? 'selected' : '' }}>{{ \App\Support\BkTingkat::label($t) }}</option>
            @endforeach
        </select>
        @error('tingkat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Deskripsi') }}</label>
        <textarea name="deskripsi" rows="3" class="{{ $inputClass }}">{{ old('deskripsi', $row->deskripsi) }}</textarea>
        @error('deskripsi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-nu-primary focus:ring-nu-primary/20" {{ old('is_active', $row->is_active ?? true) ? 'checked' : '' }} />
            {{ __('Aktif') }}
        </label>
    </div>
</div>
