<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Daftarkan sekolah baru') }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Data lembaga dan akun operator (peran admin sekolah). Jika email operator dikosongkan, sistem memakai format NPSN@:domain.', ['domain' => $defaultOperatorDomain]) }}
                </p>
            </div>
            <a href="{{ route('pengurus.sekolah.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
        <form method="POST" action="{{ route('pengurus.sekolah.store') }}" class="space-y-8">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ __('Periksa kembali input yang Anda isi.') }}
                </div>
            @endif

            @if ($cabangs->isNotEmpty())
                <div class="space-y-2">
                    <x-input-label for="cabang_id" :value="__('Cabang Maarif')" />
                    <select id="cabang_id" name="cabang_id" required class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                        <option value="">{{ __('Pilih cabang…') }}</option>
                        @foreach ($cabangs as $c)
                            <option value="{{ $c->id }}" @selected(old('cabang_id') == $c->id)>{{ $c->nama }} @if($c->kode) ({{ $c->kode }}) @endif</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cabang_id')" class="mt-1" />
                </div>
            @endif

            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Data lembaga') }}</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="npsn" :value="__('NPSN (8 digit)')" />
                        <x-text-input id="npsn" name="npsn" type="text" inputmode="numeric" maxlength="8" class="block w-full" :value="old('npsn')" required autofocus />
                        <x-input-error :messages="$errors->get('npsn')" class="mt-1" />
                    </div>
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="nama" :value="__('Nama lembaga')" />
                        <x-text-input id="nama" name="nama" type="text" class="block w-full" :value="old('nama')" required />
                        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                    </div>
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="jenjang" :value="__('Jenjang')" />
                        <select id="jenjang" name="jenjang" required class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                            @foreach (\App\Models\Sekolah::jenjangOptions() as $val => $label)
                                <option value="{{ $val }}" @selected(old('jenjang', 'sd') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('jenjang')" class="mt-1" />
                    </div>
                    @php
                        $wilayahInitial = [
                            'kode_provinsi' => old('kode_provinsi'),
                            'nama_provinsi' => old('nama_provinsi'),
                            'kode_kabupaten' => old('kode_kabupaten'),
                            'nama_kabupaten' => old('nama_kabupaten'),
                            'kode_kecamatan' => old('kode_kecamatan'),
                            'nama_kecamatan' => old('nama_kecamatan'),
                            'kode_kelurahan' => old('kode_kelurahan'),
                            'nama_kelurahan' => old('nama_kelurahan'),
                            'alamat_dusun' => old('alamat_dusun'),
                        ];
                    @endphp
                    <div class="space-y-2 sm:col-span-2">
                        <x-wilayah-alamat-fields :initial="$wilayahInitial" />
                    </div>
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="alamat" :value="__('Alamat tambahan / catatan (opsional)')" />
                        <textarea id="alamat" name="alamat" rows="2" class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">{{ old('alamat') }}</textarea>
                        <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="telepon" :value="__('Telepon')" />
                        <x-text-input id="telepon" name="telepon" type="text" class="block w-full" :value="old('telepon')" />
                        <x-input-error :messages="$errors->get('telepon')" class="mt-1" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="email_kantor" :value="__('Email kantor')" />
                        <x-text-input id="email_kantor" name="email_kantor" type="email" class="block w-full" :value="old('email_kantor')" />
                        <x-input-error :messages="$errors->get('email_kantor')" class="mt-1" />
                    </div>
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="website" :value="__('Website (opsional)')" />
                        <x-text-input id="website" name="website" type="text" class="block w-full" :value="old('website')" placeholder="https://…" />
                        <x-input-error :messages="$errors->get('website')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Kepala & akreditasi') }}</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <x-input-label for="kepala_nama" :value="__('Nama kepala lembaga')" />
                        <x-text-input id="kepala_nama" name="kepala_nama" type="text" class="block w-full" :value="old('kepala_nama')" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="kepala_nip" :value="__('NIP (opsional)')" />
                        <x-text-input id="kepala_nip" name="kepala_nip" type="text" class="block w-full" :value="old('kepala_nip')" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="akreditasi" :value="__('Akreditasi')" />
                        <x-text-input id="akreditasi" name="akreditasi" type="text" class="block w-full" :value="old('akreditasi')" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="akreditasi_tahun" :value="__('Tahun akreditasi')" />
                        <x-text-input id="akreditasi_tahun" name="akreditasi_tahun" type="text" class="block w-full" :value="old('akreditasi_tahun')" />
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Operator sekolah') }}</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="operator_name" :value="__('Nama operator')" />
                        <x-text-input id="operator_name" name="operator_name" type="text" class="block w-full" :value="old('operator_name')" required />
                        <x-input-error :messages="$errors->get('operator_name')" class="mt-1" />
                    </div>
                    <div class="space-y-2 sm:col-span-2">
                        <x-input-label for="operator_email" :value="__('Email login (opsional)')" />
                        <x-text-input id="operator_email" name="operator_email" type="email" class="block w-full" :value="old('operator_email')" placeholder="{{ __('Kosongkan untuk memakai NPSN@') }}{{ $defaultOperatorDomain }}" />
                        <p class="text-xs text-gray-500">{{ __('Jika dikosongkan, login memakai NPSN delapan digit + @') }}{{ $defaultOperatorDomain }}</p>
                        <x-input-error :messages="$errors->get('operator_email')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('pengurus.sekolah.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    {{ __('Simpan & buat akun operator') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
