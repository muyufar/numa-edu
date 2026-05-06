<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold tracking-tight text-gray-900">{{ __('Masuk') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('Gunakan akun Anda untuk mengakses dashboard.') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-4 flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-nu-primary shadow-sm focus:ring-nu-primary/25" name="remember">
                <span class="ms-2 text-sm font-medium text-gray-700">{{ __('Ingat saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-nu-primary hover:underline focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2 rounded-lg px-1" href="{{ route('password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center rounded-xl px-4 py-2.5 text-sm">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        @if (Route::has('register'))
            <p class="mt-5 text-center text-sm text-gray-600">
                {{ __('Belum punya akun?') }}
                <a href="{{ route('register') }}" class="font-semibold text-nu-primary hover:underline">{{ __('Daftar') }}</a>
            </p>
        @endif
    </form>
</x-guest-layout>
