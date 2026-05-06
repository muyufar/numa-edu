@php
    $isAdminProfilLembaga = $isAdminProfilLembaga ?? false;
    $profilLembagaUpdateRoute = $isAdminProfilLembaga
        ? route('profil-lembaga.update')
        : route('pengurus.sekolah.update', $sekolah);
    $profilLembagaBackRoute = $isAdminProfilLembaga ? route('dashboard') : route('pengurus.sekolah.index');
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Profil sekolah') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $sekolah->nama }} — NPSN {{ $sekolah->npsn }} @if($sekolah->jenjang)<span class="text-gray-400">·</span> {{ \App\Models\Sekolah::jenjangLabel($sekolah->jenjang) }}@endif</p>
                @if ($sekolah->cabang)
                    <p class="text-xs text-gray-500">{{ __('Cabang: :nama', ['nama' => $sekolah->cabang->nama]) }}</p>
                @endif
            </div>
            <a href="{{ $profilLembagaBackRoute }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif
        <form method="POST" action="{{ $profilLembagaUpdateRoute }}" class="space-y-8">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ __('Periksa kembali input yang Anda isi.') }}
                </div>
            @endif

            <div class="rounded-lg border border-gray-100 bg-gray-50/80 px-4 py-3 text-sm text-gray-600">
                <span class="font-medium text-gray-800">{{ __('NPSN') }}</span>
                {{ $sekolah->npsn }}
                <span class="mx-2 text-gray-300">|</span>
                {{ __('NPSN tidak dapat diubah dari sini.') }}
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Data lembaga') }}</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="nama" :value="__('Nama lembaga')" />
                        <x-text-input id="nama" name="nama" type="text" class="block w-full" :value="old('nama', $sekolah->nama)" required autofocus />
                        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                    </div>
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="jenjang" :value="__('Jenjang')" />
                        <select id="jenjang" name="jenjang" required class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                            @foreach (\App\Models\Sekolah::jenjangOptions() as $val => $label)
                                <option value="{{ $val }}" @selected(old('jenjang', $sekolah->jenjang ?? 'sd') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('jenjang')" class="mt-1" />
                    </div>
                    @php
                        $wilayahInitial = [
                            'kode_provinsi' => old('kode_provinsi', $sekolah->kode_provinsi),
                            'nama_provinsi' => old('nama_provinsi', $sekolah->nama_provinsi),
                            'kode_kabupaten' => old('kode_kabupaten', $sekolah->kode_kabupaten),
                            'nama_kabupaten' => old('nama_kabupaten', $sekolah->nama_kabupaten),
                            'kode_kecamatan' => old('kode_kecamatan', $sekolah->kode_kecamatan),
                            'nama_kecamatan' => old('nama_kecamatan', $sekolah->nama_kecamatan),
                            'kode_kelurahan' => old('kode_kelurahan', $sekolah->kode_kelurahan),
                            'nama_kelurahan' => old('nama_kelurahan', $sekolah->nama_kelurahan),
                            'alamat_dusun' => old('alamat_dusun', $sekolah->alamat_dusun),
                        ];
                    @endphp
                    <div class="space-y-2 sm:col-span-2">
                        <x-wilayah-alamat-fields :initial="$wilayahInitial" />
                    </div>
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="alamat" :value="__('Alamat tambahan / catatan (opsional)')" />
                        <textarea id="alamat" name="alamat" rows="2" class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">{{ old('alamat', $sekolah->alamat) }}</textarea>
                        <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="telepon" :value="__('Telepon')" />
                        <x-text-input id="telepon" name="telepon" type="text" class="block w-full" :value="old('telepon', $sekolah->telepon)" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="email_kantor" :value="__('Email kantor')" />
                        <x-text-input id="email_kantor" name="email_kantor" type="email" class="block w-full" :value="old('email_kantor', $sekolah->email_kantor)" />
                    </div>
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="website" :value="__('Website (opsional)')" />
                        <x-text-input id="website" name="website" type="text" class="block w-full" :value="old('website', $sekolah->website)" placeholder="https://…" />
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Kepala & akreditasi') }}</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <x-input-label for="kepala_nama" :value="__('Nama kepala lembaga')" />
                        <x-text-input id="kepala_nama" name="kepala_nama" type="text" class="block w-full" :value="old('kepala_nama', $sekolah->kepala_nama)" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="kepala_nip" :value="__('NIP (opsional)')" />
                        <x-text-input id="kepala_nip" name="kepala_nip" type="text" class="block w-full" :value="old('kepala_nip', $sekolah->kepala_nip)" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="akreditasi" :value="__('Akreditasi')" />
                        <x-text-input id="akreditasi" name="akreditasi" type="text" class="block w-full" :value="old('akreditasi', $sekolah->akreditasi)" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="akreditasi_tahun" :value="__('Tahun akreditasi')" />
                        <x-text-input id="akreditasi_tahun" name="akreditasi_tahun" type="text" class="block w-full" :value="old('akreditasi_tahun', $sekolah->akreditasi_tahun)" />
                    </div>
                </div>
            </div>

            @unless ($isAdminProfilLembaga)
                <div class="space-y-2 rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="mt-1 rounded border-gray-300 text-nu-primary shadow-sm focus:ring-nu-primary/25"
                            @checked(old('is_active', $sekolah->is_active))
                        />
                        <span>
                            <span class="block text-sm font-semibold text-gray-900">{{ __('Sekolah aktif') }}</span>
                            <span class="mt-0.5 block text-xs text-gray-600">{{ __('Nonaktifkan jika lembaga tidak lagi menggunakan sistem. Operator tidak dapat memilih sekolah nonaktif.') }}</span>
                        </span>
                    </label>
                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                </div>
            @endunless

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                <a href="{{ $profilLembagaBackRoute }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    {{ __('Simpan perubahan') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
