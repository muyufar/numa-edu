@php
    $h = $row ?? null;
    $inputClass = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
    $tanggalDefault = $h?->tanggal?->format('Y-m-d') ?? now()->toDateString();
    $statusOptions = [
        'draft' => __('Draft'),
        'dilaporkan' => __('Dilaporkan'),
    ];
@endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Siswa') }}</label>
        <select name="siswa_id" class="{{ $inputClass }}" required>
            <option value="">{{ __('— Pilih siswa —') }}</option>
            @foreach ($siswas as $s)
                <option value="{{ $s->id }}" {{ (string) old('siswa_id', $h?->siswa_id) === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->nis }} — {{ $s->nama }}
                </option>
            @endforeach
        </select>
        @error('siswa_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal kunjungan') }}</label>
        <input type="date" name="tanggal" value="{{ old('tanggal', $tanggalDefault) }}" class="{{ $inputClass }} font-mono" required />
        @error('tanggal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Foto dokumentasi') }}</label>
        <input type="file" name="foto" accept="image/*" class="{{ $inputClass }}" />
        <p class="mt-1 text-xs text-gray-500">{{ __('Opsional, maks. 5 MB.') }}</p>
        @error('foto')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Catatan wawancara') }}</label>
        <textarea name="catatan_wawancara" rows="3" class="{{ $inputClass }}" placeholder="{{ __('Opsional') }}">{{ old('catatan_wawancara', $h?->catatan_wawancara) }}</textarea>
        @error('catatan_wawancara')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Hasil kunjungan') }}</label>
        <textarea name="hasil_kunjungan" rows="3" class="{{ $inputClass }}" placeholder="{{ __('Opsional') }}">{{ old('hasil_kunjungan', $h?->hasil_kunjungan) }}</textarea>
        @error('hasil_kunjungan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Solusi / tindak lanjut') }}</label>
        <textarea name="solusi" rows="3" class="{{ $inputClass }}" placeholder="{{ __('Opsional') }}">{{ old('solusi', $h?->solusi) }}</textarea>
        @error('solusi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Status') }}</label>
        <select name="status" class="{{ $inputClass }}">
            @foreach ($statusOptions as $val => $label)
                <option value="{{ $val }}" {{ old('status', $h?->status ?? 'draft') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
