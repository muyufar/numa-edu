<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-900">
            {{ __('Hubungkan akun ke data sekolah') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-xl space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-600">
                @if ($user->hasRole('siswa') && $user->hasRole('wali'))
                    {{ __('Isi data berikut untuk menautkan akun siswa dan wali Anda ke data yang sudah ada di sekolah.') }}
                @elseif ($user->hasRole('siswa'))
                    {{ __('Isi data berikut agar akun Anda terhubung ke profil siswa di sekolah. Data harus sama persis dengan yang tercatat di sekolah.') }}
                @else
                    {{ __('Isi data berikut agar akun wali Anda terhubung ke data siswa. Data harus sama persis dengan yang tercatat di sekolah.') }}
                @endif
            </p>

            <form method="POST" action="{{ route('onboarding.hubungkan.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="npsn" :value="__('NPSN sekolah')" />
                    <x-text-input id="npsn" class="mt-2 block w-full" type="text" name="npsn" :value="old('npsn')" required autocomplete="off" />
                    <x-input-error :messages="$errors->get('npsn')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nis" :value="__('NIS atau NISN siswa')" />
                    <x-text-input id="nis" class="mt-2 block w-full" type="text" name="nis" :value="old('nis')" required autocomplete="off" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Isi nomor induk sekolah (NIS) atau nomor induk siswa nasional (NISN).') }}</p>
                    <x-input-error :messages="$errors->get('nis')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="tanggal_lahir" :value="__('Tanggal lahir siswa')" />
                    <x-text-input id="tanggal_lahir" class="mt-2 block w-full" type="date" name="tanggal_lahir" :value="old('tanggal_lahir')" required />
                    <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nama_siswa" :value="__('Nama lengkap siswa')" />
                    <x-text-input id="nama_siswa" class="mt-2 block w-full" type="text" name="nama_siswa" :value="old('nama_siswa')" required autocomplete="name" />
                    <x-input-error :messages="$errors->get('nama_siswa')" class="mt-2" />
                </div>

                @if ($user->hasRole('wali'))
                    <div>
                        <x-input-label for="hubungan" :value="__('Hubungan dengan siswa')" />
                        <select id="hubungan" name="hubungan" required class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-nu-primary focus:ring-nu-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-nu-primary dark:focus:ring-nu-primary">
                            <option value="" disabled @selected(old('hubungan') === null || old('hubungan') === '')>{{ __('Pilih') }}</option>
                            <option value="orang_tua" @selected(old('hubungan') === 'orang_tua')>{{ __('Orang tua') }}</option>
                            <option value="ibu" @selected(old('hubungan') === 'ibu')>{{ __('Ibu') }}</option>
                            <option value="ayah" @selected(old('hubungan') === 'ayah')>{{ __('Ayah') }}</option>
                            <option value="wali" @selected(old('hubungan') === 'wali')>{{ __('Wali') }}</option>
                            <option value="lainnya" @selected(old('hubungan') === 'lainnya')>{{ __('Lainnya') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('hubungan')" class="mt-2" />
                    </div>
                @endif

                <div class="pt-2">
                    <x-primary-button type="submit">
                        {{ __('Hubungkan') }}
                    </x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4 border-t border-gray-100 pt-4">
                @csrf
                <button type="submit" class="text-sm font-semibold text-gray-600 underline hover:text-gray-900">
                    {{ __('Keluar') }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
