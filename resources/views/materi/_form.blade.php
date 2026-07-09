@php
    /** @var \App\Models\MateriAjar|null $materi_ajar */
    $field = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
    $defaultJenis = old('jenis', $materi_ajar->jenis ?? 'modul');
    $jenisTerstruktur = ['modul', 'rpp', 'modul_pembelajaran', 'lkpd'];
@endphp

<div x-data="{ jenis: @js($defaultJenis) }" class="space-y-8">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-semibold text-gray-700">{{ __('Jenis perangkat') }}</label>
            <select name="jenis" x-model="jenis" class="{{ $field }}" required>
                @foreach (\App\Models\MateriAjar::JENIS_OPTIONS as $j)
                    <option value="{{ $j }}">{{ (new \App\Models\MateriAjar(['jenis' => $j]))->labelJenis() }}</option>
                @endforeach
            </select>
            @error('jenis')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="text-sm font-semibold text-gray-700">{{ __('Status penggunaan') }}</label>
            <select name="status_penggunaan" class="{{ $field }}" required>
                @foreach (\App\Models\MateriAjar::STATUS_PENGGUNAAN_OPTIONS as $opt)
                    <option value="{{ $opt }}" @selected(old('status_penggunaan', $materi_ajar->status_penggunaan ?? 'rencana') === $opt)>
                        {{ (new \App\Models\MateriAjar(['status_penggunaan' => $opt]))->labelStatusPenggunaan() }}
                    </option>
                @endforeach
            </select>
            @error('status_penggunaan')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
        </div>
    </div>

    @include('materi._perbandingan_jenis')

    <fieldset :disabled="jenis !== 'modul'" class="min-w-0 space-y-8 border-0 p-0 m-0" x-show="jenis === 'modul'">
        @include('materi._form_modul_merdeka', ['materi_ajar' => $materi_ajar])
    </fieldset>

    <fieldset :disabled="jenis !== 'rpp'" class="min-w-0 space-y-8 border-0 p-0 m-0" x-show="jenis === 'rpp'" x-cloak>
        @include('materi._form_rpp', ['materi_ajar' => $materi_ajar])
    </fieldset>

    <fieldset :disabled="jenis !== 'modul_pembelajaran'" class="min-w-0 space-y-8 border-0 p-0 m-0" x-show="jenis === 'modul_pembelajaran'" x-cloak>
        @include('materi._form_modul_pembelajaran', ['materi_ajar' => $materi_ajar])
    </fieldset>

    <fieldset :disabled="jenis !== 'lkpd'" class="min-w-0 space-y-8 border-0 p-0 m-0" x-show="jenis === 'lkpd'" x-cloak>
        @include('materi._form_lkpd', ['materi_ajar' => $materi_ajar])
    </fieldset>

    <fieldset :disabled="{{ json_encode($jenisTerstruktur) }}.includes(jenis)" x-show="!{{ json_encode($jenisTerstruktur) }}.includes(jenis)" x-cloak class="min-w-0 space-y-8 border-0 p-0 m-0">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Identitas perangkat ajar') }}</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">{{ __('Judul') }}</label>
                    <input name="judul" value="{{ old('judul', $materi_ajar->judul ?? '') }}" class="{{ $field }}" :required="!{{ json_encode($jenisTerstruktur) }}.includes(jenis)">
                    @error('judul')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">{{ __('Mapel') }}</label>
                    <select name="mata_pelajaran_id" class="{{ $field }}" :required="!{{ json_encode($jenisTerstruktur) }}.includes(jenis)">
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
            <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Konteks pembelajaran') }}</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
                    <select name="kelas_id" class="{{ $field }}">
                        <option value="">{{ __('Semua kelas') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" @selected((string) old('kelas_id', $materi_ajar->kelas_id ?? '') === (string) $k->id)>
                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700">{{ __('Guru') }}</label>
                    <select name="guru_id" class="{{ $field }}">
                        <option value="">{{ __('-') }}</option>
                        @foreach ($guruOptions as $g)
                            <option value="{{ $g->id }}" @selected((string) old('guru_id', $materi_ajar->guru_id ?? auth()->user()?->guru?->id) === (string) $g->id)>{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700">{{ __('Pertemuan ke-') }}</label>
                    <input type="number" min="1" max="200" name="pertemuan_ke" value="{{ old('pertemuan_ke', $materi_ajar->pertemuan_ke ?? '') }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700">{{ __('Tanggal pemakaian') }}</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', optional($materi_ajar->tanggal ?? null)->format('Y-m-d')) }}" class="{{ $field }} font-mono tabular-nums">
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
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
                    <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $materi_ajar->tahun_ajaran ?? '') }}" class="{{ $field }} font-mono tabular-nums" placeholder="2025/2026">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="text-sm font-semibold text-gray-700">{{ __('File perangkat ajar') }}</label>
                    <input type="file" name="file" class="{{ $field }} file:mr-4 file:rounded-lg file:border-0 file:bg-nu-primary/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-nu-primary" :required="!{{ json_encode($jenisTerstruktur) }}.includes(jenis) && !{{ $materi_ajar ? 'true' : 'false' }}">
                    @error('file')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
                    @if (!empty($materi_ajar?->file_name))
                        <div class="mt-2 text-xs text-gray-500">{{ __('File saat ini:') }} <span class="font-semibold">{{ $materi_ajar->file_name }}</span></div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <label class="text-sm font-semibold text-gray-700">{{ __('Deskripsi / catatan') }}</label>
            <textarea name="deskripsi" rows="4" class="{{ $field }}">{{ old('deskripsi', $materi_ajar->deskripsi ?? '') }}</textarea>
        </div>
    </fieldset>
</div>
