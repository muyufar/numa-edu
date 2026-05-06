@php
    $p = $pelanggaran ?? null;
    $tanggalDefault = $p?->tanggal?->format('Y-m-d') ?? now()->toDateString();
@endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Siswa') }}</label>
        <select name="siswa_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
            <option value="">{{ __('— Pilih siswa —') }}</option>
            @foreach ($siswas as $s)
                <option value="{{ $s->id }}" {{ (string) old('siswa_id', $p?->siswa_id) === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->nis }} — {{ $s->nama }}
                </option>
            @endforeach
        </select>
        @error('siswa_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal') }}</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', $tanggalDefault) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
            @error('tanggal')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Jenis') }}</label>
            <select name="jenis" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                <option value="">{{ __('— Pilih jenis —') }}</option>
                @foreach (\App\Models\Pelanggaran::JENIS_KEYS as $key)
                    <option value="{{ $key }}" {{ old('jenis', $p?->jenis) === $key ? 'selected' : '' }}>
                        {{ \App\Models\Pelanggaran::jenisLabel($key) }}
                    </option>
                @endforeach
            </select>
            @error('jenis')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Deskripsi kejadian') }}</label>
        <textarea name="deskripsi" rows="3" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional') }}">{{ old('deskripsi', $p?->deskripsi) }}</textarea>
        @error('deskripsi')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tindakan / pembinaan') }}</label>
        <textarea name="tindakan" rows="2" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional, mis. pembinaan lisan, surat peringatan, dsb.') }}">{{ old('tindakan', $p?->tindakan) }}</textarea>
        @error('tindakan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
