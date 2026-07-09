@php
    $r = $row ?? null;
    $tanggalDefault = $r?->tanggal?->format('Y-m-d') ?? now()->toDateString();
@endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Siswa') }}</label>
        <select name="siswa_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
            <option value="">{{ __('— Pilih siswa —') }}</option>
            @foreach ($siswas as $s)
                <option value="{{ $s->id }}" {{ (string) old('siswa_id', $r?->siswa_id) === (string) $s->id ? 'selected' : '' }}>
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
            <label class="block text-sm font-semibold text-gray-700">{{ __('Kategori') }}</label>
            <select name="kategori" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                <option value="">{{ __('— Pilih kategori —') }}</option>
                @foreach (\App\Models\RewardSiswa::KATEGORI_OPTIONS as $k)
                    <option value="{{ $k }}" {{ old('kategori', $r?->kategori) === $k ? 'selected' : '' }}>
                        {{ \App\Models\RewardSiswa::kategoriLabel($k) }}
                    </option>
                @endforeach
            </select>
            @error('kategori')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal') }}</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', $tanggalDefault) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
            @error('tanggal')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Judul') }}</label>
            <input type="text" name="judul" value="{{ old('judul', $r?->judul) }}" maxlength="160" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
            @error('judul')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Poin') }}</label>
            <input type="number" name="poin" value="{{ old('poin', $r?->poin ?? 0) }}" min="0" max="999" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
            @error('poin')
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
