<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Daftar wali') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Buat akun wali baru.') }}</p>
            </div>
            <a href="{{ route('wali-admin.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-6">
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ __('Periksa kembali input yang kamu isi.') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <form method="POST" action="{{ route('wali-admin.store') }}" class="space-y-5">
                @csrf

                @if (!empty($sekolahOptions))
                    <div>
                        <x-input-label for="sekolah_id" :value="__('Sekolah')" />
                        <select id="sekolah_id" name="sekolah_id" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25" required>
                            <option value="" disabled @selected(old('sekolah_id') === null || old('sekolah_id') === '')>{{ __('Pilih sekolah') }}</option>
                            @foreach ($sekolahOptions as $s)
                                <option value="{{ $s->id }}" @selected((string) old('sekolah_id') === (string) $s->id)>{{ $s->nama }} ({{ $s->npsn }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sekolah_id')" class="mt-2" />
                    </div>
                @endif

                <div>
                    <x-input-label for="name" :value="__('Nama wali')" />
                    <x-text-input id="name" name="name" class="mt-2 block w-full" type="text" :value="old('name')" required autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" class="mt-2 block w-full" type="email" :value="old('email')" required autocomplete="off" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('No. HP (opsional)')" />
                    <x-text-input id="phone" name="phone" class="mt-2 block w-full" type="text" :value="old('phone')" autocomplete="tel" placeholder="08xxxxxxxxxx" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" name="password" class="mt-2 block w-full" type="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" class="mt-2 block w-full" type="password" required autocomplete="new-password" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <a href="{{ route('wali-admin.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                        {{ __('Batal') }}
                    </a>
                    <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

