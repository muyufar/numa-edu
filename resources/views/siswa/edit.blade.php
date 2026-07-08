<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Edit siswa') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Perbarui data siswa.') }}</p>
                @if ($siswa->ppdbRegistration)
                    @can('view', $siswa->ppdbRegistration)
                        <p class="mt-2 text-xs text-gray-500">
                            {{ __('Dari PPDB:') }}
                            <a href="{{ route('ppdb.show', $siswa->ppdbRegistration) }}" class="font-semibold text-nu-primary hover:underline">{{ $siswa->ppdbRegistration->nama }}</a>
                        </p>
                    @endcan
                @endif
                @can('viewAny', \App\Models\Pelanggaran::class)
                    <p class="mt-1 text-xs text-gray-500">
                        <a href="{{ route('bk.pelanggaran.index', ['siswa_id' => $siswa->id, 'kelas_id' => $siswa->kelas_id]) }}" class="font-semibold text-nu-primary hover:underline">{{ __('Riwayat pelanggaran (BK)') }}</a>
                    </p>
                @endcan
            </div>
            <div class="flex flex-wrap gap-2">
                @can('create', \App\Models\PresensiSiswa::class)
                    <a href="{{ route('presensi.kartu', ['siswa', $siswa]) }}" target="_blank" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                        {{ __('Kartu presensi') }}
                    </a>
                @endcan
                <a href="{{ route('siswa.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Kembali') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-gray-600">
                {{ __('Pengaturan tambahan') }}
            </div>
            <a href="{{ route('siswa.wali.edit', $siswa) }}" class="btn-nu">
                {{ __('Kelola wali') }}
            </a>
        </div>

        <div class="mb-6 rounded-2xl border border-gray-100 bg-gray-50 p-5">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-sm font-extrabold text-gray-900">{{ __('Akun siswa') }}</div>
                    <div class="mt-1 text-xs text-gray-600">{{ __('Akun otomatis dibuat dari NISN: nisn@numaedu.id (password awal = NISN).') }}</div>
                </div>
                @if ($siswa->user)
                    <div class="text-sm font-semibold text-gray-800">{{ $siswa->user->email }}</div>
                @endif
            </div>

            @if (! $siswa->user)
                @if ($siswa->suggestedAkunEmail())
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ __('Email otomatis:') }} <span class="font-mono font-bold">{{ $siswa->suggestedAkunEmail() }}</span>
                    </div>
                    <form method="POST" action="{{ route('siswa.buat-akun', $siswa) }}" class="mt-4 grid gap-4 sm:grid-cols-12">
                        @csrf
                        <div class="sm:col-span-5">
                            <x-input-label for="akun_siswa_password" :value="__('Password kustom (opsional)')" />
                            <x-text-input id="akun_siswa_password" name="password" class="mt-2 block w-full" type="password" autocomplete="new-password" placeholder="{{ __('Kosongkan = gunakan NISN') }}" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-4">
                            <x-input-label for="akun_siswa_password_confirmation" :value="__('Konfirmasi password')" />
                            <x-text-input id="akun_siswa_password_confirmation" name="password_confirmation" class="mt-2 block w-full" type="password" autocomplete="new-password" />
                        </div>
                        <div class="sm:col-span-3 flex items-end">
                            <x-primary-button class="w-full" type="submit">{{ __('Buat akun otomatis') }}</x-primary-button>
                        </div>
                    </form>
                @else
                    <p class="mt-4 text-sm text-amber-800">{{ __('Isi NISN siswa terlebih dahulu. Akun akan dibuat otomatis saat data disimpan.') }}</p>
                @endif
            @else
                <div class="mt-4 space-y-4">
                    <div class="rounded-2xl border border-gray-200 bg-white p-4">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-gray-500">{{ __('Email akun') }}</div>
                        <form method="POST" action="{{ route('siswa.akun.update', $siswa) }}" class="mt-3 grid gap-4 sm:grid-cols-12 sm:items-end">
                            @csrf
                            @method('PUT')

                            <div class="sm:col-span-9">
                                <x-input-label for="akun_siswa_email_update" :value="__('Email akun siswa')" />
                                <x-text-input id="akun_siswa_email_update" name="email" class="mt-2 block w-full" type="email" :value="old('email', $siswa->user->email)" required autocomplete="off" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div class="sm:col-span-3">
                                <x-primary-button class="w-full" type="submit">{{ __('Simpan email') }}</x-primary-button>
                            </div>
                        </form>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-4">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-gray-500">{{ __('Reset password') }}</div>
                        <form method="POST" action="{{ route('siswa.akun.reset-password', $siswa) }}" class="mt-3 grid gap-4 sm:grid-cols-12 sm:items-end">
                            @csrf

                            <div class="sm:col-span-5">
                                <x-input-label for="akun_siswa_reset_password" :value="__('Password baru')" />
                                <x-text-input id="akun_siswa_reset_password" name="password" class="mt-2 block w-full" type="password" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="sm:col-span-5">
                                <x-input-label for="akun_siswa_reset_password_confirmation" :value="__('Konfirmasi')" />
                                <x-text-input id="akun_siswa_reset_password_confirmation" name="password_confirmation" class="mt-2 block w-full" type="password" required autocomplete="new-password" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-primary-button class="w-full" type="submit">{{ __('Reset') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('siswa.update', $siswa) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ __('Periksa kembali input yang kamu isi.') }}
                </div>
            @endif

            @include('siswa._form', ['siswa' => $siswa, 'kelasOptions' => $kelasOptions])

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('siswa.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                    {{ __('Batal') }}
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    {{ __('Simpan perubahan') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

