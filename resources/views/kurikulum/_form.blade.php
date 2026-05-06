@php
    /** @var \App\Models\KurikulumItem|null $item */
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="text-sm font-semibold text-gray-700">{{ __('Mata pelajaran') }}</label>
        <select name="mata_pelajaran_id" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm" required>
            <option value="">{{ __('Pilih mapel') }}</option>
            @foreach ($mapelOptions as $m)
                <option value="{{ $m->id }}" @selected((string) old('mata_pelajaran_id', $item?->mata_pelajaran_id ?? '') === (string) $m->id)>
                    {{ $m->kode ? $m->kode.' - ' : '' }}{{ $m->nama }}
                </option>
            @endforeach
        </select>
        @error('mata_pelajaran_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Tingkat') }}</label>
        <input type="number" name="tingkat" min="1" max="12" value="{{ old('tingkat', $item?->tingkat ?? '') }}" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm" required>
        @error('tingkat')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Semester') }}</label>
        <select name="semester" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm" required>
            <option value="1" @selected(old('semester', $item?->semester ?? request('semester', '1')) === '1')>1</option>
            <option value="2" @selected(old('semester', $item?->semester ?? request('semester', '1')) === '2')>2</option>
        </select>
        @error('semester')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
        <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $item?->tahun_ajaran ?? request('tahun_ajaran', '')) }}" placeholder="2025/2026" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm" required>
        @error('tahun_ajaran')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Jam per minggu') }}</label>
        <input type="number" name="jam_per_minggu" min="0" max="40" value="{{ old('jam_per_minggu', $item?->jam_per_minggu ?? '') }}" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm" placeholder="{{ __('Opsional') }}">
        @error('jam_per_minggu')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Urutan') }}</label>
        <input type="number" name="urutan" min="0" value="{{ old('urutan', $item?->urutan ?? 0) }}" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm">
        @error('urutan')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div class="sm:col-span-2 flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $item?->is_active ?? true))>
        <span class="text-sm font-semibold text-gray-700">{{ __('Aktif') }}</span>
        @error('is_active')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="text-sm font-semibold text-gray-700">{{ __('Catatan') }}</label>
        <textarea name="catatan" rows="3" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm" placeholder="{{ __('Opsional') }}">{{ old('catatan', $item?->catatan ?? '') }}</textarea>
        @error('catatan')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>
</div>
