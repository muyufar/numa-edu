@props(['tagihan' => null, 'siswaOptions'])

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Siswa') }}</label>
        <select name="siswa_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
            <option value="">{{ __('— Pilih siswa —') }}</option>
            @foreach ($siswaOptions as $s)
                <option value="{{ $s->id }}" {{ (string) old('siswa_id', $tagihan?->siswa_id) === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->nama }} ({{ $s->nis }})@if($s->kelas) — {{ $s->kelas->tingkat }} {{ $s->kelas->nama }}@endif
                </option>
            @endforeach
        </select>
        @error('siswa_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Jenis tagihan') }}</label>
        <input list="jenis-tagihan-saran" name="jenis" type="text" maxlength="32" value="{{ old('jenis', $tagihan?->jenis) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="SPP, DU, UKM, …" required />
        <datalist id="jenis-tagihan-saran">
            <option value="SPP"></option>
            <option value="DU"></option>
            <option value="UKM"></option>
            <option value="Komite"></option>
            <option value="Seragam"></option>
            <option value="Kegiatan"></option>
        </datalist>
        @error('jenis')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Periode') }}</label>
        <input name="periode" type="text" maxlength="7" value="{{ old('periode', $tagihan?->periode) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="2026-04" required />
        <p class="mt-1 text-xs text-gray-500">{{ __('Wajib format: YYYY-MM (contoh 2026-04).') }}</p>
        @error('periode')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Jumlah tagihan') }}</label>
        <input name="jumlah" type="number" step="0.01" min="0" value="{{ old('jumlah', $tagihan?->jumlah) }}" class="mt-2 w-full max-w-xs rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
        @error('jumlah')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Jatuh tempo') }} <span class="font-normal text-gray-500">({{ __('opsional') }})</span></label>
        <input name="jatuh_tempo" type="date" value="{{ old('jatuh_tempo', $tagihan?->jatuh_tempo?->format('Y-m-d')) }}" class="mt-2 w-full max-w-xs rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
        @error('jatuh_tempo')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
