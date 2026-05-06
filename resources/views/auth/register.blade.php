<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold tracking-tight text-gray-900">{{ __('Daftar akun') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('Buat akun untuk mengelola sekolah di dashboard.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Jenis akun -->
        <div class="mt-4">
            <x-input-label for="jenis_akun" :value="__('Jenis akun')" />
            <select id="jenis_akun" name="jenis_akun" required class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-nu-primary focus:ring-nu-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-nu-primary dark:focus:ring-nu-primary">
                <option value="" disabled @selected(old('jenis_akun') === null || old('jenis_akun') === '')>{{ __('Pilih jenis akun') }}</option>
                <option value="wali" @selected(old('jenis_akun') === 'wali')>{{ __('Wali murid') }}</option>
                <option value="siswa" @selected(old('jenis_akun') === 'siswa')>{{ __('Siswa') }}</option>
            </select>
            <x-input-error :messages="$errors->get('jenis_akun')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="mt-2 block w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center rounded-xl px-4 py-2.5 text-sm">
                {{ __('Register') }}
            </x-primary-button>
        </div>

        <p class="mt-5 text-center text-sm text-gray-600">
            {{ __('Sudah punya akun?') }}
            <a href="{{ route('login') }}" class="font-semibold text-nu-primary hover:underline">{{ __('Masuk') }}</a>
        </p>
    </form>
</x-guest-layout>
