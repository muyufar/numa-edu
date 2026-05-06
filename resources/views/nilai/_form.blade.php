@props(['nilai' => null, 'siswaOptions', 'kelasOptions', 'mapelOptions', 'tahunAjaranOptions'])

<div class="grid gap-4 sm:grid-cols-2">
    <input type="hidden" name="kelas_id" value="{{ old('kelas_id', $nilai?->kelas_id) }}" />

    <div class="sm:col-span-2 rounded-xl border border-gray-100 bg-gray-50/80 px-4 py-3 text-sm text-gray-700">
        @php
            $kid = old('kelas_id', $nilai?->kelas_id);
            $k = $kelasOptions->firstWhere('id', (int) $kid);
        @endphp
        @if ($k)
            <span class="font-semibold text-gray-900">{{ __('Kelas') }}:</span>
            {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
        @else
            <span class="text-gray-500">{{ __('Kelas belum dipilih.') }}</span>
        @endif
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Mata pelajaran') }}</label>
        <select name="mata_pelajaran_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
            <option value="">{{ __('— Pilih mapel —') }}</option>
            @foreach ($mapelOptions as $m)
                <option value="{{ $m->id }}" {{ (string) old('mata_pelajaran_id', $nilai?->mata_pelajaran_id) === (string) $m->id ? 'selected' : '' }}>
                    {{ $m->nama }} ({{ $m->kode }})
                </option>
            @endforeach
        </select>
        @error('mata_pelajaran_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Semester') }}</label>
        <select name="semester" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
            @foreach (\App\Models\Nilai::SEMESTER_OPTIONS as $s)
                <option value="{{ $s }}" {{ (string) old('semester', $nilai?->semester) === (string) $s ? 'selected' : '' }}>
                    {{ $s === '1' ? __('Semester 1 (Ganjil)') : __('Semester 2 (Genap)') }}
                </option>
            @endforeach
        </select>
        @error('semester')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
        @if ($tahunAjaranOptions->isNotEmpty())
            <select name="tahun_ajaran" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                @foreach ($tahunAjaranOptions as $t)
                    <option value="{{ $t }}" {{ (string) old('tahun_ajaran', $nilai?->tahun_ajaran) === (string) $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        @else
            <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $nilai?->tahun_ajaran) }}" maxlength="16" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="2025/2026" required />
        @endif
        @error('tahun_ajaran')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Siswa') }}</label>
        <select name="siswa_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
            <option value="">{{ __('— Pilih siswa —') }}</option>
            @foreach ($siswaOptions as $s)
                <option value="{{ $s->id }}" {{ (string) old('siswa_id', $nilai?->siswa_id) === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->nama }} ({{ $s->nis }})
                </option>
            @endforeach
        </select>
        @error('siswa_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nilai akhir') }} <span class="font-normal text-gray-500">(0–100, opsional)</span></label>
        <input name="nilai_akhir" type="number" step="0.01" min="0" max="100" value="{{ old('nilai_akhir', $nilai?->nilai_akhir) }}" class="mt-2 w-full max-w-xs rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
        @error('nilai_akhir')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
