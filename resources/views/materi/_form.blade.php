@php
    /** @var \App\Models\MateriAjar|null $materi_ajar */
    $field = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
@endphp

<div class="space-y-8">
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Informasi materi') }}</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Judul') }}</label>
                <input name="judul" value="{{ old('judul', $materi_ajar->judul ?? '') }}" class="{{ $field }}" required>
                @error('judul')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Mapel') }}</label>
                <select name="mata_pelajaran_id" class="{{ $field }}" required>
                    <option value="">{{ __('Pilih mapel') }}</option>
                    @foreach ($mapelOptions as $m)
                        <option value="{{ $m->id }}" @selected((string) old('mata_pelajaran_id', $materi_ajar->mata_pelajaran_id ?? '') === (string) $m->id)>
                            {{ $m->kode ? $m->kode.' - ' : '' }}{{ $m->nama }}
                        </option>
                    @endforeach
                </select>
                @error('mata_pelajaran_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Konteks (opsional)') }}</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
                <select name="kelas_id" class="{{ $field }}">
                    <option value="">{{ __('Semua kelas') }}</option>
                    @foreach ($kelasOptions as $k)
                        <option value="{{ $k->id }}" @selected((string) old('kelas_id', $materi_ajar->kelas_id ?? '') === (string) $k->id)>
                            {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}{{ $k->is_active ? '' : ' (nonaktif)' }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Guru') }}</label>
                <select name="guru_id" class="{{ $field }}">
                    <option value="">{{ __('-') }}</option>
                    @foreach ($guruOptions as $g)
                        <option value="{{ $g->id }}" @selected((string) old('guru_id', $materi_ajar->guru_id ?? '') === (string) $g->id)>{{ $g->nama }}</option>
                    @endforeach
                </select>
                @error('guru_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Periode & berkas') }}</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Semester') }}</label>
                <select name="semester" class="{{ $field }}">
                    <option value="">{{ __('-') }}</option>
                    <option value="1" @selected(old('semester', $materi_ajar->semester ?? '') === '1')>1</option>
                    <option value="2" @selected(old('semester', $materi_ajar->semester ?? '') === '2')>2</option>
                </select>
                @error('semester')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
                <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $materi_ajar->tahun_ajaran ?? '') }}" class="{{ $field }} font-mono tabular-nums" placeholder="2025/2026">
                @error('tahun_ajaran')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="text-sm font-semibold text-gray-700">{{ __('Tanggal') }}</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', optional($materi_ajar->tanggal ?? null)->format('Y-m-d')) }}" class="{{ $field }} font-mono tabular-nums">
                @error('tanggal')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="text-sm font-semibold text-gray-700">{{ __('File') }}</label>
                <input type="file" name="file" class="{{ $field }} file:mr-4 file:rounded-lg file:border-0 file:bg-nu-primary/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-nu-primary hover:file:bg-nu-primary/15">
                @error('file')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
                @if (!empty($materi_ajar?->file_name))
                    <div class="mt-2 text-xs text-gray-500">{{ __('File saat ini:') }} <span class="font-semibold">{{ $materi_ajar->file_name }}</span></div>
                @endif
            </div>
        </div>
    </div>

    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Deskripsi') }}</label>
        <textarea name="deskripsi" rows="4" class="{{ $field }}" placeholder="{{ __('Opsional') }}">{{ old('deskripsi', $materi_ajar->deskripsi ?? '') }}</textarea>
        @error('deskripsi')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>
</div>
