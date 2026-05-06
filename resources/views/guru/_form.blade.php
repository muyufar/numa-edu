@props(['guru' => null])

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('NIP') }}</label>
        <input
            name="nip"
            type="text"
            maxlength="32"
            value="{{ old('nip', $guru?->nip) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="{{ __('Opsional') }}"
        />
        @error('nip')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Telepon') }}</label>
        <input
            name="phone"
            type="text"
            maxlength="20"
            value="{{ old('phone', $guru?->phone) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="{{ __('Opsional') }}"
        />
        @error('phone')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama lengkap') }}</label>
        <input
            name="nama"
            type="text"
            value="{{ old('nama', $guru?->nama) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        />
        @error('nama')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Email (untuk masuk)') }}</label>
        <input
            name="email"
            type="email"
            value="{{ old('email', $guru?->user?->email) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        />
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ $guru ? __('Kata sandi baru') : __('Kata sandi') }}</label>
        <input
            name="password"
            type="password"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            {{ $guru ? '' : 'required' }}
            autocomplete="new-password"
        />
        @if ($guru)
            <p class="mt-1 text-xs text-gray-500">{{ __('Kosongkan jika tidak ingin mengubah kata sandi.') }}</p>
        @endif
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Ulangi kata sandi') }}</label>
        <input
            name="password_confirmation"
            type="password"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            {{ $guru ? '' : 'required' }}
            autocomplete="new-password"
        />
    </div>
</div>
