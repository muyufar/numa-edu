@php
    /** @var \App\Models\Tugas|null $tugas */
    $field = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
    $jamDefault = $tugas?->jam ? substr((string) $tugas->jam, 0, 5) : '';
    $jamBatasDefault = $tugas?->jam_batas ? substr((string) $tugas->jam_batas, 0, 5) : '';
@endphp

<div class="space-y-8">
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Informasi tugas') }}</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Judul tugas') }}</label>
                <input name="judul" value="{{ old('judul', $tugas?->judul ?? '') }}" class="{{ $field }}" required placeholder="{{ __('Contoh: LKPD Bab 3 — Persamaan Linear') }}">
                @error('judul')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Mapel') }}</label>
                <select name="mata_pelajaran_id" class="{{ $field }}" required>
                    <option value="">{{ __('Pilih mapel') }}</option>
                    @foreach ($mapelOptions as $m)
                        <option value="{{ $m->id }}" @selected((string) old('mata_pelajaran_id', $tugas?->mata_pelajaran_id ?? '') === (string) $m->id)>
                            {{ $m->kode ? $m->kode.' - ' : '' }}{{ $m->nama }}
                        </option>
                    @endforeach
                </select>
                @error('mata_pelajaran_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
                <select name="kelas_id" class="{{ $field }}">
                    <option value="">{{ __('Semua kelas') }}</option>
                    @foreach ($kelasOptions as $k)
                        <option value="{{ $k->id }}" @selected((string) old('kelas_id', $tugas?->kelas_id ?? '') === (string) $k->id)>
                            {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}{{ $k->is_active ? '' : ' (nonaktif)' }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Guru pengampu') }}</label>
                <select name="guru_id" class="{{ $field }}">
                    <option value="">{{ __('-') }}</option>
                    @foreach ($guruOptions as $g)
                        <option value="{{ $g->id }}" @selected((string) old('guru_id', $tugas?->guru_id ?? '') === (string) $g->id)>{{ $g->nama }}</option>
                    @endforeach
                </select>
                @error('guru_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Tipe penugasan') }}</label>
                <select name="tipe" class="{{ $field }}" required>
                    @foreach (\App\Models\Tugas::TIPE_OPTIONS as $tipe)
                        <option value="{{ $tipe }}" @selected(old('tipe', $tugas?->tipe ?? 'individu') === $tipe)>
                            {{ \App\Models\Tugas::tipeLabel($tipe) }}
                        </option>
                    @endforeach
                </select>
                @error('tipe')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Bobot / poin') }}</label>
                <input type="number" name="bobot" min="0" max="1000" value="{{ old('bobot', $tugas?->bobot ?? '') }}" class="{{ $field }} font-mono tabular-nums" placeholder="{{ __('Opsional') }}">
                @error('bobot')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Jadwal penugasan') }}</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Hari') }}</label>
                <select name="hari" class="{{ $field }}">
                    <option value="">{{ __('-') }}</option>
                    @foreach (\App\Models\Tugas::HARI_OPTIONS as $h)
                        <option value="{{ $h }}" @selected(old('hari', $tugas?->hari ?? '') === $h)>{{ $h }}</option>
                    @endforeach
                </select>
                @error('hari')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Jam') }}</label>
                <input type="time" name="jam" value="{{ old('jam', $jamDefault) }}" class="{{ $field }} font-mono tabular-nums">
                @error('jam')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Tanggal batas kumpul') }}</label>
                <input type="date" name="tanggal_batas" value="{{ old('tanggal_batas', optional($tugas?->tanggal_batas)->format('Y-m-d')) }}" class="{{ $field }} font-mono tabular-nums">
                @error('tanggal_batas')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Jam batas kumpul') }}</label>
                <input type="time" name="jam_batas" value="{{ old('jam_batas', $jamBatasDefault) }}" class="{{ $field }} font-mono tabular-nums">
                @error('jam_batas')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Semester') }}</label>
                <select name="semester" class="{{ $field }}">
                    <option value="">{{ __('-') }}</option>
                    <option value="1" @selected(old('semester', $tugas?->semester ?? '') === '1')>1</option>
                    <option value="2" @selected(old('semester', $tugas?->semester ?? '') === '2')>2</option>
                </select>
                @error('semester')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
                <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $tugas?->tahun_ajaran ?? '') }}" class="{{ $field }} font-mono tabular-nums" placeholder="2025/2026">
                @error('tahun_ajaran')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Bahan materi / soal') }}</h3>
        <div class="mt-4 space-y-4">
            @include('tugas.partials.soal-builder', ['tugas' => $tugas, 'field' => $field])

            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Instruksi pengerjaan') }}</label>
                <textarea name="instruksi" rows="4" class="{{ $field }}" placeholder="{{ __('Cara pengumpulan, format jawaban, catatan khusus...') }}">{{ old('instruksi', $tugas?->instruksi ?? '') }}</textarea>
                @error('instruksi')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Lampiran berkas') }}</label>
                <input type="file" name="file" class="{{ $field }} file:mr-4 file:rounded-lg file:border-0 file:bg-nu-primary/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-nu-primary hover:file:bg-nu-primary/15">
                @error('file')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
                @if (!empty($tugas?->file_name))
                    <div class="mt-2 text-xs text-gray-500">{{ __('File saat ini:') }} <span class="font-semibold">{{ $tugas->file_name }}</span></div>
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4">
        <label class="inline-flex cursor-pointer items-center gap-3">
            <input type="checkbox" name="is_published" value="1" class="rounded border-gray-300 text-nu-primary focus:ring-nu-primary/20"
                @checked(old('is_published', $tugas?->is_published ?? true))>
            <span>
                <span class="block text-sm font-semibold text-gray-900">{{ __('Publikasikan ke siswa') }}</span>
                <span class="block text-xs text-gray-500">{{ __('Jika tidak dicentang, tugas hanya terlihat oleh admin/guru.') }}</span>
            </span>
        </label>
        @error('is_published')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>
</div>
