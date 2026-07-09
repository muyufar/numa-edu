@php
    use App\Support\PerangkatAjarJenis;

    /** @var \App\Models\MateriAjar|null $materi_ajar */
    $field = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
    $konten = PerangkatAjarJenis::normalizeKonten('rpp', old('konten_modul', $materi_ajar?->konten_modul));
@endphp

<div class="space-y-8">
    <div class="rounded-2xl border border-amber-200/80 bg-amber-50/60 p-4 text-sm text-amber-950">
        <p class="font-semibold text-amber-900">{{ __('RPP — Rencana Pelaksanaan Pembelajaran') }}</p>
        <p class="mt-1">{{ PerangkatAjarJenis::deskripsiJenis('rpp') }}</p>
        <p class="mt-2 text-xs text-amber-800">{{ __('Fokus:') }} <span class="font-semibold">{{ PerangkatAjarJenis::fokusJenis('rpp') }}</span> · {{ __('Isi langkah pembelajaran & penilaian hasil belajar.') }}</p>
    </div>

    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Identitas RPP') }}</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Judul / topik pembelajaran') }}</label>
                <input name="judul" value="{{ old('judul', $materi_ajar->judul ?? '') }}" class="{{ $field }}" placeholder="Contoh: Mengenal Bagian Tubuh dan Fungsinya" required>
                @error('judul')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Topik / materi pokok') }}</label>
                <input name="elemen_topik" value="{{ old('elemen_topik', $materi_ajar->elemen_topik ?? '') }}" class="{{ $field }}" placeholder="{{ __('Opsional — sub-topik atau materi pokok') }}">
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
                <label class="text-sm font-semibold text-gray-700">{{ __('Guru') }}</label>
                <select name="guru_id" class="{{ $field }}">
                    <option value="">{{ __('-') }}</option>
                    @foreach ($guruOptions as $g)
                        <option value="{{ $g->id }}" @selected((string) old('guru_id', $materi_ajar->guru_id ?? auth()->user()?->guru?->id) === (string) $g->id)>{{ $g->nama }}</option>
                    @endforeach
                </select>
                @error('guru_id')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Pertemuan ke-') }}</label>
                <input type="number" min="1" max="200" name="pertemuan_ke" value="{{ old('pertemuan_ke', $materi_ajar->pertemuan_ke ?? '') }}" class="{{ $field }}">
                @error('pertemuan_ke')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Alokasi waktu') }}</label>
                <input name="alokasi_waktu" value="{{ old('alokasi_waktu', $materi_ajar->alokasi_waktu ?? '') }}" class="{{ $field }}" placeholder="2 JP (2 x 45 menit)">
                @error('alokasi_waktu')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-gray-700">{{ __('Model pembelajaran') }}</label>
                <input name="model_pembelajaran" list="model-pembelajaran-rpp" value="{{ old('model_pembelajaran', $materi_ajar->model_pembelajaran ?? '') }}" class="{{ $field }}" placeholder="Discovery Learning">
                <datalist id="model-pembelajaran-rpp">
                    @foreach (\App\Support\ModulAjarMerdeka::MODEL_PEMBELAJARAN_OPTIONS as $model)
                        <option value="{{ $model }}"></option>
                    @endforeach
                </datalist>
                @error('model_pembelajaran')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
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
                <label class="text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
                <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $materi_ajar->tahun_ajaran ?? '') }}" class="{{ $field }} font-mono" placeholder="2025/2026">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">{{ __('Tanggal pembelajaran') }}</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', optional($materi_ajar->tanggal ?? null)->format('Y-m-d')) }}" class="{{ $field }} font-mono">
            </div>
        </div>
    </div>

    @include('materi._konten_sections_fields', ['jenis' => 'rpp', 'konten' => $konten, 'fieldClass' => $field])

    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Catatan tambahan') }}</label>
        <textarea name="deskripsi" rows="3" class="{{ $field }}" placeholder="{{ __('Opsional') }}">{{ old('deskripsi', $materi_ajar->deskripsi ?? '') }}</textarea>
    </div>

    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Unggah berkas RPP (opsional)') }}</label>
        <input type="file" name="file" accept=".pdf,.doc,.docx,application/pdf" class="{{ $field }} file:mr-4 file:rounded-lg file:border-0 file:bg-nu-primary/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-nu-primary">
        @error('file')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
        @if (!empty($materi_ajar?->file_name))
            <div class="mt-2 text-xs text-gray-500">{{ __('File saat ini:') }} <span class="font-semibold">{{ $materi_ajar->file_name }}</span></div>
        @endif
    </div>
</div>
