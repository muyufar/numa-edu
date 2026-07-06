@props(['registration' => null, 'showStatus' => false])

<div class="space-y-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama calon siswa') }}</label>
        <input name="nama" type="text" value="{{ old('nama', $registration?->nama) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
        @error('nama')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tempat lahir') }}</label>
            <input name="tempat_lahir" type="text" value="{{ old('tempat_lahir', $registration?->tempat_lahir) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('tempat_lahir')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal lahir') }}</label>
            <input name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $registration?->tanggal_lahir?->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('tanggal_lahir')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Jenis kelamin') }}</label>
        <select name="jenis_kelamin" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
            <option value="">{{ __('—') }}</option>
            <option value="L" {{ old('jenis_kelamin', $registration?->jenis_kelamin) === 'L' ? 'selected' : '' }}>{{ __('Laki-laki') }}</option>
            <option value="P" {{ old('jenis_kelamin', $registration?->jenis_kelamin) === 'P' ? 'selected' : '' }}>{{ __('Perempuan') }}</option>
        </select>
        @error('jenis_kelamin')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Alamat') }}</label>
        <textarea name="alamat" rows="3" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">{{ old('alamat', $registration?->alamat) }}</textarea>
        @error('alamat')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Asal sekolah') }}</label>
        <input name="asal_sekolah" type="text" value="{{ old('asal_sekolah', $registration?->asal_sekolah) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
        @error('asal_sekolah')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('No. HP orang tua') }}</label>
            <input name="no_hp_ortu" type="text" maxlength="32" value="{{ old('no_hp_ortu', $registration?->no_hp_ortu) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('no_hp_ortu')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Email') }}</label>
            <input name="email" type="email" maxlength="128" value="{{ old('email', $registration?->email) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    @if ($showStatus)
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Status') }}</label>
            <select name="status" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                @foreach (\App\Models\PpdbRegistration::STATUS_OPTIONS as $st)
                    <option value="{{ $st }}" {{ old('status', $registration?->status) === $st ? 'selected' : '' }}>
                        {{ \App\Models\PpdbRegistration::statusLabel($st) }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endif
</div>
