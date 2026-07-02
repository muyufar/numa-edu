@php
    $reg = $registration ?? null;
    $isEditForm = (bool) ($isEdit ?? false);
    $permitsByKey = $reg ? $reg->permits->keyBy('permit_key') : collect();
    $fv = fn (string $field) => old($field, $reg !== null ? $reg->getAttribute($field) : null);
    $lokasiInitial = [
        'base' => rtrim((string) url('ref/wilayah'), '/'),
        'nominatimBase' => rtrim((string) url('/ref/nominatim'), '/'),
        'alamat_jalan' => old('alamat_jalan', $reg?->alamat_jalan),
        'rt' => old('rt', $reg?->rt),
        'rw' => old('rw', $reg?->rw),
        'kodepos' => old('kodepos', $reg?->kodepos),
        'provinsi' => old('provinsi', $reg?->provinsi),
        'kabupaten_kota' => old('kabupaten_kota', $reg?->kabupaten_kota),
        'kecamatan' => old('kecamatan', $reg?->kecamatan),
        'desa_kelurahan' => old('desa_kelurahan', $reg?->desa_kelurahan),
    ];
@endphp
<x-public-wide-layout :title="$isEditForm ? __('Perbaiki pendaftaran lembaga') : __('Pendaftaran lembaga')">
    {{-- Hero: selalu terlihat (tanpa x-cloak) --}}
    <div class="mb-6 rounded-2xl border border-nu-primary/15 bg-gradient-to-br from-white via-white to-nu-primary/[0.04] p-5 shadow-sm ring-1 ring-black/[0.03] sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">{{ $isEditForm ? __('Perbaiki pendaftaran lembaga') : __('Pendaftaran lembaga') }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-gray-600">
                    @if ($isEditForm)
                        {{ __('Perbarui data sesuai catatan verifikator, lalu kirim ulang. Foto dan PDF yang sudah ada tidak wajib diunggah ulang kecuali ingin diganti.') }}
                    @else
                        {{ __('Lengkapi data per langkah. Gunakan «Sebelumnya» dan «Berikutnya». Di langkah terakhir kirim formulir untuk melanjutkan ke MoU.') }}
                    @endif
                </p>
            </div>
            <a
                href="{{ route('public.lembaga-registrations.check-status') }}"
                class="inline-flex shrink-0 items-center justify-center rounded-xl border border-nu-primary/30 bg-white px-4 py-2.5 text-sm font-semibold text-nu-primary shadow-sm hover:bg-nu-primary/5 focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2"
            >
                {{ __('Cek status (NPSN)') }}
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
            {{ __('Periksa kembali isian pada formulir.') }}
        </div>
    @endif

    <div class="mx-auto w-full max-w-full" x-data="lembagaWizard({{ (int) $wizardInitialStep }}, @js($wizardFileSkips ?? []))" x-cloak>
        {{-- Indikator langkah (Alpine) --}}
        <div class="mb-6 rounded-2xl border border-gray-200/80 bg-white/90 px-4 py-4 shadow-sm sm:px-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap gap-2 text-[11px] font-bold sm:text-xs">
                    @foreach ([
                        1 => __('Identitas'),
                        2 => __('Lokasi'),
                        3 => __('Galeri'),
                        4 => __('Perijinan'),
                        5 => __('Operator'),
                    ] as $num => $label)
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 ring-1 transition sm:px-3"
                            :class="{
                                'bg-nu-primary text-white ring-nu-primary': step === {{ $num }},
                                'bg-emerald-50 text-emerald-900 ring-emerald-200': step > {{ $num }},
                                'bg-gray-100 text-gray-600 ring-gray-200': step < {{ $num }},
                            }"
                        >
                            <span class="tabular-nums">{{ $num }}</span>
                            <span class="max-w-[5.5rem] truncate sm:max-w-none">{{ $label }}</span>
                        </span>
                    @endforeach
                </div>
                <p class="shrink-0 text-xs font-semibold text-gray-500">
                    {{ __('Langkah') }}
                    <span class="tabular-nums text-nu-primary" x-text="step">1</span>/<span class="tabular-nums" x-text="maxStep">5</span>
                </p>
            </div>
        </div>

        <form
            x-ref="form"
            method="POST"
            action="{{ $isEditForm ? route('public.lembaga-registrations.update', ['token' => $reg->public_token]) : route('public.lembaga-registrations.store') }}"
            enctype="multipart/form-data"
            class="space-y-6"
            novalidate
            @submit.prevent="onFormSubmit()"
        >
            @csrf
            @if ($isEditForm)
                @method('PUT')
            @endif

            {{-- 1. Identitas --}}
            <div x-show="step === 1" x-transition.opacity.duration.200ms class="space-y-0">
                <article class="overflow-hidden rounded-2xl border border-gray-200/90 bg-white shadow-md ring-1 ring-black/[0.04]">
                    <header class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-nu-primary/[0.06] to-transparent px-4 py-4 sm:flex-row sm:items-center sm:gap-4 sm:px-6 sm:py-5">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-nu-primary text-base font-bold text-white shadow-sm" aria-hidden="true">1</span>
                        <div class="min-w-0">
                            <h2 class="text-base font-bold text-gray-900 sm:text-lg">{{ __('Identitas lembaga') }}</h2>
                            <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">{{ __('NPSN, nama lembaga, kontak, jenjang, dan informasi operasional.') }}</p>
                        </div>
                    </header>
                    <div class="space-y-4 px-4 py-5 sm:px-6 sm:py-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2 sm:col-span-2">
                                <x-input-label for="npsn" :value="__('NPSN (8 digit)')" />
                                <x-text-input id="npsn" name="npsn" type="text" inputmode="numeric" maxlength="8" class="block w-full" :value="$fv('npsn')" autofocus />
                                <x-input-error :messages="$errors->get('npsn')" class="mt-1" />
                            </div>
                            <div class="space-y-2 sm:col-span-2">
                                <x-input-label for="nama_lembaga" :value="__('Nama lembaga')" />
                                <x-text-input id="nama_lembaga" name="nama_lembaga" type="text" class="block w-full" :value="$fv('nama_lembaga')" />
                                <x-input-error :messages="$errors->get('nama_lembaga')" class="mt-1" />
                            </div>
                            <div class="space-y-2 sm:col-span-2">
                                <x-input-label for="nama_kepala" :value="__('Nama kepala sekolah / madrasah')" />
                                <x-text-input id="nama_kepala" name="nama_kepala" type="text" class="block w-full" :value="$fv('nama_kepala')" />
                                <x-input-error :messages="$errors->get('nama_kepala')" class="mt-1" />
                            </div>
                            <div class="space-y-2 sm:col-span-2">
                                <x-input-label for="jenjang" :value="__('Jenis / jenjang sekolah')" />
                                <select id="jenjang" name="jenjang" class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                                    @foreach (\App\Models\Sekolah::jenjangOptions() as $val => $label)
                                        <option value="{{ $val }}" @selected($fv('jenjang') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('jenjang')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="npwp" :value="__('NPWP')" />
                                <x-text-input id="npwp" name="npwp" type="text" class="block w-full" :value="$fv('npwp')" />
                                <x-input-error :messages="$errors->get('npwp')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="telepon" :value="__('Nomor telepon')" />
                                <x-text-input id="telepon" name="telepon" type="text" class="block w-full" :value="$fv('telepon')" />
                                <x-input-error :messages="$errors->get('telepon')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="website" :value="__('Alamat web')" />
                                <x-text-input id="website" name="website" type="text" class="block w-full" :value="$fv('website')" placeholder="https://…" />
                                <x-input-error :messages="$errors->get('website')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="block w-full" :value="$fv('email')" />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>
                            <div class="space-y-2 sm:col-span-2">
                                <x-input-label for="medsos" :value="__('Media sosial')" />
                                <x-text-input id="medsos" name="medsos" type="text" class="block w-full" :value="$fv('medsos')" placeholder="{{ __('Contoh: Instagram @akun') }}" />
                                <x-input-error :messages="$errors->get('medsos')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="tahun_berdiri" :value="__('Tahun berdiri')" />
                                <x-text-input id="tahun_berdiri" name="tahun_berdiri" type="number" min="1900" max="{{ (int) date('Y') }}" class="block w-full" :value="$fv('tahun_berdiri')" />
                                <x-input-error :messages="$errors->get('tahun_berdiri')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="waktu_belajar" :value="__('Waktu belajar')" />
                                <select id="waktu_belajar" name="waktu_belajar" class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                                    <option value="pagi" @selected($fv('waktu_belajar') === 'pagi')>{{ __('Pagi') }}</option>
                                    <option value="siang" @selected($fv('waktu_belajar') === 'siang')>{{ __('Siang') }}</option>
                                    <option value="pagi_siang" @selected($fv('waktu_belajar') === 'pagi_siang')>{{ __('Pagi dan siang') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('waktu_belajar')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="status_kkm" :value="__('Status KKM')" />
                                <select id="status_kkm" name="status_kkm" class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                                    <option value="induk" @selected($fv('status_kkm') === 'induk')>{{ __('Induk') }}</option>
                                    <option value="anggota" @selected($fv('status_kkm') === 'anggota')>{{ __('Anggota') }}</option>
                                    <option value="tidak" @selected($fv('status_kkm') === 'tidak')>{{ __('Tidak masuk KKM') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('status_kkm')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="komite" :value="__('Komite lembaga')" />
                                <select id="komite" name="komite" class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                                    <option value="sudah" @selected($fv('komite') === 'sudah')>{{ __('Sudah terbentuk') }}</option>
                                    <option value="belum" @selected($fv('komite') === 'belum')>{{ __('Belum terbentuk') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('komite')" class="mt-1" />
                            </div>
                            <div class="space-y-2 sm:col-span-2">
                                <x-input-label for="jumlah_murid" :value="__('Jumlah murid terkini (wajib)')" />
                                <p class="text-xs text-gray-500">{{ __('Isi jumlah peserta didik saat ini di lembaga Anda (angka bulat).') }}</p>
                                <x-text-input id="jumlah_murid" name="jumlah_murid" type="number" min="0" max="999999" step="1" class="block w-full max-w-xs" :value="old('jumlah_murid', $reg?->jumlah_murid)" required inputmode="numeric" />
                                <x-input-error :messages="$errors->get('jumlah_murid')" class="mt-1" />
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            {{-- 2. Lokasi (wilayah.id via API + peta OSM + pencarian Nominatim) --}}
            <div x-show="step === 2" x-transition.opacity.duration.200ms class="space-y-0">
                <article class="overflow-hidden rounded-2xl border border-gray-200/90 bg-white shadow-md ring-1 ring-black/[0.04]">
                    <header class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-nu-primary/[0.06] to-transparent px-4 py-4 sm:flex-row sm:items-center sm:gap-4 sm:px-6 sm:py-5">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-nu-primary text-base font-bold text-white shadow-sm" aria-hidden="true">2</span>
                        <div class="min-w-0">
                            <h2 class="text-base font-bold text-gray-900 sm:text-lg">{{ __('Lokasi') }}</h2>
                            <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">{{ __('Pilih wilayah dari data resmi, lalu lengkapi alamat. Gunakan pencarian atau klik peta untuk mengisi alamat dari OpenStreetMap (bisa disesuaikan).') }}</p>
                        </div>
                    </header>
                    <div class="space-y-4 px-4 py-5 sm:px-6 sm:py-6" x-data="lembagaLokasiWilayah(@js($lokasiInitial))">
                        <p x-show="wilayahErr" x-cloak class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800" x-text="wilayahErr"></p>
                        <p x-show="mapErr" x-cloak class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900" x-text="mapErr"></p>

                        <div class="space-y-3">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                                <div class="min-w-0 flex-1 space-y-1">
                                    <x-input-label for="lembaga_map_search" :value="__('Cari lokasi di peta')" />
                                    <input
                                        id="lembaga_map_search"
                                        type="search"
                                        autocomplete="off"
                                        x-model="searchQuery"
                                        @keydown.enter.prevent="runMapSearch()"
                                        placeholder="{{ __('Contoh: nama jalan, kelurahan, atau sekolah di Indonesia') }}"
                                        class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                                    />
                                </div>
                                <button
                                    type="button"
                                    class="inline-flex shrink-0 items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light disabled:opacity-50"
                                    :disabled="searchLoading"
                                    @click="runMapSearch()"
                                >
                                    <span x-show="!searchLoading">{{ __('Cari') }}</span>
                                    <span x-show="searchLoading" x-cloak>{{ __('Mencari…') }}</span>
                                </button>
                            </div>
                            <p x-show="searchErr" x-cloak class="text-sm text-amber-800" x-text="searchErr"></p>
                            <ul
                                x-show="searchResults.length"
                                x-cloak
                                class="max-h-40 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50/80 text-sm ring-1 ring-black/5"
                            >
                                <template x-for="(row, idx) in searchResults" :key="idx">
                                    <li class="border-b border-gray-100 last:border-0">
                                        <button
                                            type="button"
                                            class="w-full px-3 py-2 text-left hover:bg-white"
                                            @click="pickSearchResult(row)"
                                            x-text="row.display_name"
                                        ></button>
                                    </li>
                                </template>
                            </ul>

                            <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-3 ring-1 ring-black/[0.04] sm:p-4">
                                <p class="text-xs font-semibold text-gray-700">{{ __('Koordinat GPS (opsional, WGS84)') }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">{{ __('Jika Anda punya lintang & bujur pasti (mis. dari GPS), masukkan lalu terapkan ke peta. Koma atau titik boleh dipakai sebagai desimal.') }}</p>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <div class="space-y-1">
                                        <x-input-label for="lembaga_manual_lat" :value="__('Lintang (latitude)')" />
                                        <input
                                            id="lembaga_manual_lat"
                                            type="text"
                                            inputmode="decimal"
                                            autocomplete="off"
                                            x-model="manualLat"
                                            @keydown.enter.prevent="applyManualCoordinates()"
                                            placeholder="-7.5551234"
                                            class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                                        />
                                    </div>
                                    <div class="space-y-1">
                                        <x-input-label for="lembaga_manual_lng" :value="__('Bujur (longitude)')" />
                                        <input
                                            id="lembaga_manual_lng"
                                            type="text"
                                            inputmode="decimal"
                                            autocomplete="off"
                                            x-model="manualLng"
                                            @keydown.enter.prevent="applyManualCoordinates()"
                                            placeholder="110.2054321"
                                            class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                                        />
                                    </div>
                                </div>
                                <p x-show="coordErr" x-cloak class="mt-2 text-sm text-amber-800" x-text="coordErr"></p>
                                <button
                                    type="button"
                                    class="mt-3 inline-flex items-center justify-center rounded-xl border border-nu-primary/40 bg-white px-4 py-2 text-sm font-semibold text-nu-primary shadow-sm hover:bg-nu-primary/[0.06]"
                                    @click="applyManualCoordinates()"
                                >
                                    {{ __('Terapkan ke peta & isi alamat') }}
                                </button>
                            </div>

                            <div class="space-y-1">
                                <x-input-label :value="__('Titik lokasi di peta (OpenStreetMap)')" />
                                <p class="text-xs text-gray-500">{{ __('Klik pada peta untuk mengisi alamat & kode pos; pencarian atau koordinat manual memindahkan penanda.') }}</p>
                                <div id="lembaga-osm-map" class="relative z-0 h-56 w-full overflow-hidden rounded-xl border border-gray-200 ring-1 ring-black/5"></div>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2 sm:col-span-2">
                                <x-input-label for="lembaga_alamat_jalan" :value="__('Alamat (jalan / detail)')" />
                                <textarea id="lembaga_alamat_jalan" name="alamat_jalan" rows="3" x-model="alamatJalan" class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"></textarea>
                                <x-input-error :messages="$errors->get('alamat_jalan')" class="mt-1" />
                            </div>
                            <div class="grid grid-cols-3 gap-3 sm:col-span-2 sm:grid-cols-6">
                                <div class="col-span-1 space-y-2 sm:col-span-1">
                                    <x-input-label for="lembaga_rt" :value="__('RT')" />
                                    <x-text-input id="lembaga_rt" name="rt" type="text" class="block w-full" x-model="rt" />
                                </div>
                                <div class="col-span-1 space-y-2 sm:col-span-1">
                                    <x-input-label for="lembaga_rw" :value="__('RW')" />
                                    <x-text-input id="lembaga_rw" name="rw" type="text" class="block w-full" x-model="rw" />
                                </div>
                                <div class="col-span-3 space-y-2 sm:col-span-4">
                                    <x-input-label for="lembaga_wilayah_kel" :value="__('Desa / kelurahan')" />
                                    <select
                                        id="lembaga_wilayah_kel"
                                        x-model="kodeKel"
                                        @change="onKelChange()"
                                        class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                                        :disabled="!kodeKec"
                                    >
                                        <option value="">{{ __('Pilih kelurahan/desa…') }}</option>
                                        <template x-for="p in villages" :key="p.code">
                                            <option :value="p.code" x-text="p.name"></option>
                                        </template>
                                    </select>
                                    <input type="hidden" name="desa_kelurahan" :value="namaKel" />
                                    <x-input-error :messages="$errors->get('desa_kelurahan')" class="mt-1" />
                                </div>
                            </div>
                            <div class="space-y-2 sm:col-span-2">
                                <x-input-label for="lembaga_wilayah_kec" :value="__('Kecamatan')" />
                                <select
                                    id="lembaga_wilayah_kec"
                                    x-model="kodeKec"
                                    @change="onKecChange()"
                                    class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                                    :disabled="!kodeKab"
                                >
                                    <option value="">{{ __('Pilih kecamatan…') }}</option>
                                    <template x-for="p in districts" :key="p.code">
                                        <option :value="p.code" x-text="p.name"></option>
                                    </template>
                                </select>
                                <input type="hidden" name="kecamatan" :value="namaKec" />
                                <x-input-error :messages="$errors->get('kecamatan')" class="mt-1" />
                            </div>
                            <div class="space-y-2 sm:col-span-2">
                                <x-input-label for="lembaga_wilayah_kab" :value="__('Kabupaten / kota')" />
                                <select
                                    id="lembaga_wilayah_kab"
                                    x-model="kodeKab"
                                    @change="onKabChange()"
                                    class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                                    :disabled="!kodeProv"
                                >
                                    <option value="">{{ __('Pilih kabupaten/kota…') }}</option>
                                    <template x-for="p in regencies" :key="p.code">
                                        <option :value="p.code" x-text="p.name"></option>
                                    </template>
                                </select>
                                <input type="hidden" name="kabupaten_kota" :value="namaKab" />
                                <x-input-error :messages="$errors->get('kabupaten_kota')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="lembaga_wilayah_prov" :value="__('Provinsi')" />
                                <select
                                    id="lembaga_wilayah_prov"
                                    x-model="kodeProv"
                                    @change="onProvChange()"
                                    class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                                >
                                    <option value="">{{ __('Pilih provinsi…') }}</option>
                                    <template x-for="p in provinces" :key="p.code">
                                        <option :value="p.code" x-text="p.name"></option>
                                    </template>
                                </select>
                                <input type="hidden" name="provinsi" :value="namaProv" />
                                <x-input-error :messages="$errors->get('provinsi')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="lembaga_kodepos" :value="__('Kode pos')" />
                                <x-text-input id="lembaga_kodepos" name="kodepos" type="text" class="block w-full" x-model="kodepos" />
                                <x-input-error :messages="$errors->get('kodepos')" class="mt-1" />
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            {{-- 3. Galeri --}}
            <div x-show="step === 3" x-transition.opacity.duration.200ms class="space-y-0">
                <article class="overflow-hidden rounded-2xl border border-gray-200/90 bg-white shadow-md ring-1 ring-black/[0.04]">
                    <header class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-nu-primary/[0.06] to-transparent px-4 py-4 sm:flex-row sm:items-center sm:gap-4 sm:px-6 sm:py-5">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-nu-primary text-base font-bold text-white shadow-sm" aria-hidden="true">3</span>
                        <div class="min-w-0">
                            <h2 class="text-base font-bold text-gray-900 sm:text-lg">{{ __('Galeri foto') }}</h2>
                            <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">{{ __('Unggah foto JPG atau PNG, maksimal 5 MB per berkas.') }}</p>
                        </div>
                    </header>
                    <div class="px-4 py-5 sm:px-6 sm:py-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ([
                                ['name' => 'foto_papan_nama', 'path' => 'foto_papan_nama_path', 'label' => __('Foto papan nama'), 'hint' => __('Tampak papan nama resmi lembaga.')],
                                ['name' => 'foto_gedung_depan', 'path' => 'foto_gedung_path', 'label' => __('Foto gedung (tampak depan)'), 'hint' => __('Bangunan utama dari depan.')],
                                ['name' => 'foto_kelas', 'path' => 'foto_kelas_path', 'label' => __('Foto kelas'), 'hint' => __('Ruang belajar mengajar.')],
                                ['name' => 'foto_halaman', 'path' => 'foto_halaman_path', 'label' => __('Foto halaman'), 'hint' => __('Halaman atau area luar sekolah.')],
                            ] as $f)
                                @php
                                    $existingFoto = $reg?->getAttribute($f['path']);
                                @endphp
                                <div class="group flex flex-col rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/40 p-4 transition hover:border-nu-primary/35 hover:bg-white hover:shadow-md sm:p-5">
                                    <x-input-label :for="$f['name']" :value="$f['label']" class="text-sm font-bold text-gray-900" />
                                    <p class="mt-1 text-xs text-gray-500">{{ $f['hint'] }}</p>
                                    @if ($existingFoto)
                                        <p class="mt-2 text-xs text-gray-600">
                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($existingFoto) }}" class="font-semibold text-nu-primary underline" target="_blank" rel="noopener">{{ __('Lihat foto yang sudah diunggah') }}</a>
                                            <span class="text-gray-500"> — {{ __('Unggah berkas baru di bawah jika ingin mengganti.') }}</span>
                                        </p>
                                    @endif
                                    <div class="mt-3 flex min-h-[2.75rem] flex-1 items-end">
                                        <input id="{{ $f['name'] }}" name="{{ $f['name'] }}" type="file" accept="image/*" class="block w-full text-xs text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-nu-primary file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-nu-primary-light sm:text-sm sm:file:text-sm" />
                                    </div>
                                    <x-input-error :messages="$errors->get($f['name'])" class="mt-2" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>
            </div>

            {{-- 4. Perijinan --}}
            <div x-show="step === 4" x-transition.opacity.duration.200ms class="space-y-0">
                <article class="overflow-hidden rounded-2xl border border-gray-200/90 bg-white shadow-md ring-1 ring-black/[0.04]">
                    <header class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-nu-primary/[0.06] to-transparent px-4 py-4 sm:flex-row sm:items-center sm:gap-4 sm:px-6 sm:py-5">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-nu-primary text-base font-bold text-white shadow-sm" aria-hidden="true">4</span>
                        <div class="min-w-0">
                            <h2 class="text-base font-bold text-gray-900 sm:text-lg">{{ __('Dokumen perijinan') }}</h2>
                            <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">{{ __('Isi nomor dan tanggal SK; unggah PDF bila tersedia (maks. 12 MB).') }}</p>
                        </div>
                    </header>
                    <div class="px-4 py-5 sm:px-6 sm:py-6">
                        <div class="space-y-3 lg:space-y-0 lg:overflow-hidden lg:rounded-xl lg:border lg:border-gray-100">
                            <div class="hidden bg-gray-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-gray-600 lg:grid lg:grid-cols-12 lg:gap-3">
                                <div class="col-span-1">{{ __('No.') }}</div>
                                <div class="col-span-4">{{ __('Nama SK') }}</div>
                                <div class="col-span-2">{{ __('Nomor SK') }}</div>
                                <div class="col-span-2">{{ __('Tanggal SK') }}</div>
                                <div class="col-span-3">{{ __('Dokumen (PDF)') }}</div>
                            </div>
                            @foreach ($permitDefs as $i => $def)
                                @php
                                    $k = $def['key'];
                                    $perm = $permitsByKey->get($k);
                                @endphp
                                <div class="rounded-2xl border border-gray-200 bg-gray-50/40 p-4 shadow-sm lg:rounded-none lg:border-0 lg:border-t lg:border-gray-100 lg:bg-white lg:p-3 lg:shadow-none">
                                    <div class="lg:grid lg:grid-cols-12 lg:items-start lg:gap-3">
                                        <div class="mb-2 flex items-center gap-2 lg:col-span-1 lg:mb-0 lg:block lg:pt-2">
                                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-nu-primary/10 text-xs font-bold text-nu-primary lg:bg-transparent lg:p-0">{{ $i + 1 }}</span>
                                            <p class="text-sm font-semibold leading-snug text-gray-900 lg:hidden">{{ $def['label'] }}</p>
                                        </div>
                                        <p class="mb-3 hidden text-xs font-medium leading-snug text-gray-900 lg:col-span-4 lg:mb-0 lg:block lg:pt-2 lg:text-sm">{{ $def['label'] }}</p>
                                        <div class="space-y-3 lg:col-span-7 lg:grid lg:grid-cols-7 lg:gap-3 lg:space-y-0">
                                            <div class="space-y-1 lg:col-span-3">
                                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 lg:hidden">{{ __('Nomor SK') }}</span>
                                                <input type="text" name="permits[{{ $k }}][nomor_sk]" value="{{ old('permits.'.$k.'.nomor_sk', $perm?->nomor_sk ?? '') }}" placeholder="{{ __('Nomor SK') }}" class="block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-nu-primary focus:ring-nu-primary/25 lg:placeholder-transparent" />
                                            </div>
                                            <div class="space-y-1 lg:col-span-2">
                                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 lg:hidden">{{ __('Tanggal SK') }}</span>
                                                <input type="date" name="permits[{{ $k }}][tanggal_sk]" value="{{ old('permits.'.$k.'.tanggal_sk', optional($perm?->tanggal_sk)->format('Y-m-d') ?? '') }}" class="block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-nu-primary focus:ring-nu-primary/25" />
                                            </div>
                                            <div class="space-y-1 lg:col-span-2">
                                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 lg:hidden">{{ __('PDF') }}</span>
                                                @if ($perm?->dokumen_path)
                                                    <p class="mb-1 text-xs text-gray-600">
                                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($perm->dokumen_path) }}" class="font-semibold text-nu-primary underline" target="_blank" rel="noopener">{{ __('Lihat PDF terunggah') }}</a>
                                                        <span class="text-gray-500"> — {{ __('Pilih berkas baru untuk mengganti.') }}</span>
                                                    </p>
                                                @endif
                                                <input type="file" name="permits[{{ $k }}][dokumen]" accept="application/pdf" class="block w-full text-xs file:rounded-lg file:border-0 file:bg-nu-primary file:px-3 file:py-2 file:font-semibold file:text-white hover:file:bg-nu-primary-light sm:text-sm" />
                                                <x-input-error :messages="$errors->get('permits.'.$k.'.dokumen')" class="mt-1" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>
            </div>

            {{-- 5. Operator --}}
            <div x-show="step === 5" x-transition.opacity.duration.200ms class="space-y-0">
                <article class="overflow-hidden rounded-2xl border border-gray-200/90 bg-white shadow-md ring-1 ring-black/[0.04]">
                    <header class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-nu-primary/[0.06] to-transparent px-4 py-4 sm:flex-row sm:items-center sm:gap-4 sm:px-6 sm:py-5">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-nu-primary text-base font-bold text-white shadow-sm" aria-hidden="true">5</span>
                        <div class="min-w-0">
                            <h2 class="text-base font-bold text-gray-900 sm:text-lg">{{ __('Akun admin sekolah (operator)') }}</h2>
                            <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">{{ __('Digunakan untuk login setelah persetujuan LP Ma’arif / PCNU.') }}</p>
                        </div>
                    </header>
                    <div class="grid gap-4 px-4 py-5 sm:grid-cols-2 sm:px-6 sm:py-6">
                        <div class="space-y-2 sm:col-span-2">
                            <x-input-label for="operator_name" :value="__('Nama operator / penanggung jawab akun')" />
                            <x-text-input id="operator_name" name="operator_name" type="text" class="block w-full" :value="$fv('operator_name')" />
                            <x-input-error :messages="$errors->get('operator_name')" class="mt-1" />
                        </div>
                        <div class="space-y-2 sm:col-span-2">
                            <x-input-label for="operator_email" :value="__('Email untuk login sistem')" />
                            <x-text-input id="operator_email" name="operator_email" type="email" class="block w-full" :value="$fv('operator_email')" />
                            <x-input-error :messages="$errors->get('operator_email')" class="mt-1" />
                        </div>
                    </div>
                </article>
            </div>

            {{-- Navigasi wizard --}}
            <div class="sticky bottom-3 z-20 rounded-2xl border border-gray-200/90 bg-white/95 p-4 shadow-lg shadow-gray-900/10 backdrop-blur-md sm:static sm:border sm:bg-white sm:p-5 sm:shadow-md">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ $isEditForm ? route('public.lembaga-registrations.status', ['token' => $reg->public_token]) : url('/') }}" class="order-last text-center text-sm font-semibold text-gray-600 hover:text-gray-900 sm:order-first sm:text-left">{{ __('Batal') }}</a>
                    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center sm:justify-end sm:gap-3">
                        <button
                            type="button"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 sm:w-auto"
                            @click="prev()"
                            :disabled="step === 1"
                        >
                            {{ __('Sebelumnya') }}
                        </button>
                        <button
                            type="button"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-nu-primary-light sm:w-auto"
                            x-show="step < maxStep"
                            @click="next()"
                        >
                            {{ __('Berikutnya') }}
                        </button>
                        <button
                            type="button"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-nu-primary-light sm:w-auto"
                            x-show="step === maxStep"
                            @click="submitWizard()"
                        >
                            {{ $isEditForm ? __('Kirim ulang untuk verifikasi') : __('Simpan & lanjut ke MoU') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-public-wide-layout>
