@php
    $r = $row ?? null;
    $tanggalDefault = $r?->tanggal?->format('Y-m-d') ?? now()->toDateString();
    $selectedIds = array_map('strval', (array) ($selectedSiswaIds ?? []));
    $hideKelasSelect = isset($hideKelasSelect) && $hideKelasSelect;
@endphp

<div class="space-y-5">
    @if ($hideKelasSelect && ($kelasId ?? null))
        <input type="hidden" name="kelas_id" value="{{ $kelasId }}" />
    @else
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
            <select name="kelas_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                <option value="">{{ __('— Opsional —') }}</option>
                @foreach ($kelasOptions ?? [] as $k)
                    <option value="{{ $k->id }}" {{ (string) old('kelas_id', $r?->kelas_id ?? ($kelasId ?? '')) === (string) $k->id ? 'selected' : '' }}>
                        {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
                    </option>
                @endforeach
            </select>
            @error('kelas_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Judul') }}</label>
            <input type="text" name="judul" value="{{ old('judul', $r?->judul) }}" maxlength="160" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
            @error('judul')
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
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tempat') }}</label>
            <input type="text" name="tempat" value="{{ old('tempat', $r?->tempat) }}" maxlength="160" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('tempat')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Status') }}</label>
            <select name="status" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                <option value="draft" {{ old('status', $r?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                <option value="publish" {{ old('status', $r?->status) === 'publish' ? 'selected' : '' }}>{{ __('Publish') }}</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Laporan') }}</label>
        <textarea name="laporan" rows="4" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional') }}">{{ old('laporan', $r?->laporan) }}</textarea>
        @error('laporan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('File LKPD') }}</label>
        @if ($r?->lkpd_name)
            <p class="mt-1 text-xs text-gray-600">
                {{ __('File saat ini') }}:
                @if ($r->lkpdUrl())
                    <a href="{{ $r->lkpdUrl() }}" target="_blank" class="font-semibold text-nu-primary hover:underline">{{ $r->lkpd_name }}</a>
                @else
                    <span>{{ $r->lkpd_name }}</span>
                @endif
            </p>
        @endif
        <input type="file" name="lkpd" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-nu-primary/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-nu-primary focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
        <p class="mt-1 text-xs text-gray-500">{{ __('Opsional, maks. 10 MB.') }}</p>
        @error('lkpd')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @if (isset($siswas) && $siswas->isNotEmpty())
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Peserta / anggota') }}</label>
            <p class="mt-1 text-xs text-gray-500">{{ __('Centang siswa yang mengikuti kegiatan ini.') }}</p>
            <fieldset class="mt-3 max-h-64 space-y-2 overflow-y-auto rounded-xl border border-gray-100 p-3">
                @foreach ($siswas as $s)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-gray-50">
                        <input
                            type="checkbox"
                            name="siswa_ids[]"
                            value="{{ $s->id }}"
                            class="rounded border-gray-300 text-nu-primary focus:ring-nu-primary/25"
                            @checked(in_array((string) $s->id, old('siswa_ids', $selectedIds), true))
                        />
                        <span class="text-sm text-gray-800">{{ $s->nama }}</span>
                        <span class="font-mono text-xs text-gray-500">NIS {{ $s->nis }}</span>
                    </label>
                @endforeach
            </fieldset>
            @error('siswa_ids')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endif
</div>
