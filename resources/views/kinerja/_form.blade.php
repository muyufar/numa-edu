@php
    /** @var \App\Models\KinerjaPenilaian|null $item */
@endphp

<div class="grid gap-4 lg:grid-cols-12">
    <div class="lg:col-span-5">
        <label class="text-sm font-semibold text-gray-700">{{ __('Target') }}</label>
        <div class="mt-2 flex flex-wrap gap-2">
            <label class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold">
                <input type="radio" name="target_type" value="guru" class="rounded border-gray-300" @checked(old('target_type', $item->target_type ?? 'guru') === 'guru')>
                <span>{{ __('Guru') }}</span>
            </label>
            <label class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold">
                <input type="radio" name="target_type" value="pegawai" class="rounded border-gray-300" @checked(old('target_type', $item->target_type ?? 'guru') === 'pegawai')>
                <span>{{ __('Pegawai') }}</span>
            </label>
        </div>
        @error('target_type')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="lg:col-span-7">
        <label class="text-sm font-semibold text-gray-700">{{ __('Nama') }}</label>
        <div class="mt-2 grid gap-3 sm:grid-cols-2">
            <div>
                <select name="guru_id" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                    <option value="">{{ __('Pilih guru') }}</option>
                    @foreach ($gurus as $g)
                        <option value="{{ $g->id }}" @selected((string) old('guru_id', $item->guru_id ?? '') === (string) $g->id)>{{ $g->nama }}</option>
                    @endforeach
                </select>
                @error('guru_id')
                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <select name="pegawai_id" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                    <option value="">{{ __('Pilih pegawai') }}</option>
                    @foreach ($pegawais as $p)
                        <option value="{{ $p->id }}" @selected((string) old('pegawai_id', $item->pegawai_id ?? '') === (string) $p->id)>{{ $p->nama }}</option>
                    @endforeach
                </select>
                @error('pegawai_id')
                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mt-2 text-xs text-gray-500">{{ __('Isi salah satu sesuai target yang dipilih.') }}</div>
    </div>

    <div class="lg:col-span-4">
        <label class="text-sm font-semibold text-gray-700">{{ __('Tanggal') }}</label>
        <input type="date" name="tanggal" value="{{ old('tanggal', optional($item->tanggal ?? null)->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm">
        @error('tanggal')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="lg:col-span-4">
        <label class="text-sm font-semibold text-gray-700">{{ __('Periode (YYYY-MM)') }}</label>
        <input type="text" name="periode" placeholder="2026-04" value="{{ old('periode', $item->periode ?? now()->format('Y-m')) }}" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm">
        @error('periode')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="lg:col-span-4">
        <label class="text-sm font-semibold text-gray-700">{{ __('Skor (0-100)') }}</label>
        <input type="number" name="skor" min="0" max="100" value="{{ old('skor', $item->skor ?? 0) }}" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm">
        @error('skor')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="lg:col-span-12">
        <label class="text-sm font-semibold text-gray-700">{{ __('Aspek') }}</label>
        <input type="text" name="aspek" value="{{ old('aspek', $item->aspek ?? '') }}" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm" placeholder="{{ __('Contoh: Disiplin, Administrasi, Pelayanan, Kesiapan mengajar...') }}">
        @error('aspek')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="lg:col-span-12">
        <label class="text-sm font-semibold text-gray-700">{{ __('Catatan') }}</label>
        <textarea name="catatan" rows="4" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm" placeholder="{{ __('Opsional') }}">{{ old('catatan', $item->catatan ?? '') }}</textarea>
        @error('catatan')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>

