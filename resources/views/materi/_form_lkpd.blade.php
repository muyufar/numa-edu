@php
    use App\Support\LkpdSistematika;
    use App\Support\PerangkatAjarJenis;

    /** @var \App\Models\MateriAjar|null $materi_ajar */
    $field = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
    $konten = LkpdSistematika::normalizeKonten(old('konten_modul', $materi_ajar?->konten_modul));
    $defaultSistematika = old('lkpd_sistematika', $konten[LkpdSistematika::META_SISTEMATIKA] ?? LkpdSistematika::defaultAlternatif());
    $fieldsByGroup = collect(LkpdSistematika::kontenFieldDefinitions())
        ->except(LkpdSistematika::META_SISTEMATIKA)
        ->groupBy('group');
@endphp

<div
    x-data="{ sistematika: @js($defaultSistematika) }"
    class="space-y-8"
>
    <div class="rounded-2xl border border-violet-200/80 bg-violet-50/60 p-4 text-sm text-violet-950">
        <p class="font-semibold text-violet-900">{{ __('LKPD — Lembar Kerja Peserta Didik') }}</p>
        <p class="mt-1">{{ PerangkatAjarJenis::deskripsiJenis('lkpd') }}</p>
        <p class="mt-2 text-xs text-violet-800">{{ __('Pilih sistematika sesuai format yang digunakan sekolah.') }}</p>
    </div>

    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Sistematika LKPD') }}</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Format sistematika') }}</label>
                <select name="lkpd_sistematika" x-model="sistematika" class="{{ $field }}">
                    @foreach (LkpdSistematika::ALTERNATIF_OPTIONS as $key => $label)
                        <option value="{{ $key }}">{{ __($label) }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    <span x-show="sistematika === 'alternatif_1'">{{ __('Alternatif 1: diakhiri dengan Soal-soal.') }}</span>
                    <span x-show="sistematika === 'alternatif_2'" x-cloak>{{ __('Alternatif 2: mencakup Alat dan Bahan, Tugas yang Harus Dilakukan, dan Hasil Penyelesaian Tugas.') }}</span>
                </p>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Identitas LKPD') }}</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Judul') }}</label>
                <input name="judul" value="{{ old('judul', $materi_ajar->judul ?? '') }}" class="{{ $field }}" placeholder="Contoh: LKPD Mengenal Bagian Tubuh" required>
                @error('judul')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Materi ajar') }}</label>
                <input name="elemen_topik" value="{{ old('elemen_topik', $materi_ajar->elemen_topik ?? '') }}" class="{{ $field }}" placeholder="Contoh: Mengenal Bagian Tubuh dan Fungsinya">
                @error('elemen_topik')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
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
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
                <select name="kelas_id" class="{{ $field }}">
                    <option value="">{{ __('Pilih kelas') }}</option>
                    @foreach ($kelasOptions as $k)
                        <option value="{{ $k->id }}" @selected((string) old('kelas_id', $materi_ajar->kelas_id ?? '') === (string) $k->id)>
                            {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Semester') }}</label>
                <select name="semester" class="{{ $field }}">
                    <option value="">{{ __('-') }}</option>
                    <option value="1" @selected(old('semester', $materi_ajar->semester ?? '') === '1')>1</option>
                    <option value="2" @selected(old('semester', $materi_ajar->semester ?? '') === '2')>2</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Alokasi waktu') }}</label>
                <input name="alokasi_waktu" value="{{ old('alokasi_waktu', $materi_ajar->alokasi_waktu ?? '') }}" class="{{ $field }}" placeholder="2 x 45 menit">
                @error('alokasi_waktu')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Guru / penulis') }}</label>
                <select name="guru_id" class="{{ $field }}">
                    <option value="">{{ __('-') }}</option>
                    @foreach ($guruOptions as $g)
                        <option value="{{ $g->id }}" @selected((string) old('guru_id', $materi_ajar->guru_id ?? auth()->user()?->guru?->id) === (string) $g->id)>{{ $g->nama }}</option>
                    @endforeach
                </select>
                @error('guru_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
                <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $materi_ajar->tahun_ajaran ?? '') }}" class="{{ $field }} font-mono" placeholder="2025/2026">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Pertemuan ke-') }}</label>
                <input type="number" min="1" max="200" name="pertemuan_ke" value="{{ old('pertemuan_ke', $materi_ajar->pertemuan_ke ?? '') }}" class="{{ $field }}">
            </div>
        </div>
        <p class="mt-2 text-xs text-gray-500">{{ __('Satuan pendidikan diisi otomatis dari data sekolah saat ditampilkan.') }}</p>
    </div>

    @foreach (LkpdSistematika::groupLabels() as $groupKey => $groupLabel)
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __($groupLabel) }}</h3>
            <div class="mt-4 space-y-4">
                @foreach ($fieldsByGroup->get($groupKey, []) as $fieldKey => $meta)
                    <div
                        x-show="sistematika === '{{ $meta['alternatif'] }}' || '{{ $meta['alternatif'] }}' === 'both'"
                        x-cloak
                    >
                        <label class="text-sm font-semibold text-gray-700">{{ __($meta['label']) }}</label>
                        <textarea
                            name="konten_modul[{{ $fieldKey }}]"
                            rows="{{ $meta['rows'] }}"
                            class="{{ $field }}"
                            placeholder="{{ $meta['placeholder'] ?? '' }}"
                        >{{ $konten[$fieldKey] ?? '' }}</textarea>
                        @error("konten_modul.$fieldKey")<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Catatan tambahan') }}</label>
        <textarea name="deskripsi" rows="3" class="{{ $field }}" placeholder="{{ __('Opsional') }}">{{ old('deskripsi', $materi_ajar->deskripsi ?? '') }}</textarea>
    </div>

    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Unggah berkas LKPD (opsional)') }}</label>
        <input type="file" name="file" accept=".pdf,.doc,.docx,application/pdf" class="{{ $field }} file:mr-4 file:rounded-lg file:border-0 file:bg-nu-primary/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-nu-primary">
        @error('file')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
        @if (!empty($materi_ajar?->file_name))
            <div class="mt-2 text-xs text-gray-500">{{ __('File saat ini:') }} <span class="font-semibold">{{ $materi_ajar->file_name }}</span></div>
        @endif
    </div>
</div>
