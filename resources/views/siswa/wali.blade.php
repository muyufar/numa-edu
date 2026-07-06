<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Kelola wali') }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Siswa:') }} <span class="font-semibold text-gray-900">{{ $siswa->nama }}</span> · {{ __('NIS') }}: <span class="font-semibold text-gray-900">{{ $siswa->nis }}</span>@if ($siswa->nisn) · {{ __('NISN') }}: <span class="font-semibold text-gray-900">{{ $siswa->nisn }}</span>@endif
                </p>
            </div>
            <a href="{{ route('siswa.edit', $siswa) }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="text-lg font-extrabold text-gray-900">{{ __('Buat akun wali baru') }}</div>
                <p class="mt-1 text-xs text-gray-500">{{ __('Akun wali dibuat oleh admin sekolah dan otomatis ditautkan ke siswa ini.') }}</p>

                <form method="POST" action="{{ route('siswa.wali.buat-akun', $siswa) }}" class="mt-4 grid gap-4 sm:grid-cols-12">
                    @csrf
                    <div class="sm:col-span-4">
                        <x-input-label for="wali_name" :value="__('Nama wali')" />
                        <x-text-input id="wali_name" name="name" class="mt-2 block w-full" type="text" :value="old('name')" required autocomplete="name" placeholder="Nama wali" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-4">
                        <x-input-label for="wali_email" :value="__('Email')" />
                        <x-text-input id="wali_email" name="email" class="mt-2 block w-full" type="email" :value="old('email')" required autocomplete="off" placeholder="wali@contoh.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="wali_password" :value="__('Password')" />
                        <x-text-input id="wali_password" name="password" class="mt-2 block w-full" type="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="wali_password_confirmation" :value="__('Konfirmasi')" />
                        <x-text-input id="wali_password_confirmation" name="password_confirmation" class="mt-2 block w-full" type="password" required autocomplete="new-password" />
                    </div>
                    <div class="sm:col-span-4">
                        <x-input-label for="wali_hubungan" :value="__('Hubungan')" />
                        <x-text-input id="wali_hubungan" name="hubungan" class="mt-2 block w-full" type="text" :value="old('hubungan', 'orang_tua')" required autocomplete="off" placeholder="orang_tua" />
                        <x-input-error :messages="$errors->get('hubungan')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2 flex items-end">
                        <x-primary-button class="w-full" type="submit">{{ __('Buat & tautkan') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="text-lg font-extrabold text-gray-900">{{ __('Tautkan wali') }}</div>
                <form method="POST" action="{{ route('siswa.wali.store', $siswa) }}" class="mt-4 grid gap-4 sm:grid-cols-12">
                    @csrf
                    <div class="sm:col-span-6">
                        <x-input-label for="wali_existing_user_id" :value="__('User wali')" />
                        <select id="wali_existing_user_id" name="user_id" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                            <option value="">{{ __('Pilih wali') }}</option>
                            @foreach ($waliUsers as $u)
                                <option value="{{ $u->id }}" @selected((string) old('user_id') === (string) $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-4">
                        <x-input-label for="wali_existing_hubungan" :value="__('Hubungan')" />
                        <x-text-input id="wali_existing_hubungan" name="hubungan" class="mt-2 block w-full" type="text" :value="old('hubungan', 'orang_tua')" required autocomplete="off" placeholder="orang_tua" />
                        <x-input-error :messages="$errors->get('hubungan')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2 flex items-end">
                        <x-primary-button class="w-full" type="submit">{{ __('Tautkan') }}</x-primary-button>
                    </div>
                </form>
                <div class="mt-3 text-xs text-gray-500">{{ __('Catatan: user harus memiliki role "wali".') }}</div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="text-lg font-extrabold text-gray-900">{{ __('Daftar wali') }}</div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                <th class="py-2 pr-4">{{ __('Nama') }}</th>
                                <th class="py-2 pr-4">{{ __('Email') }}</th>
                                <th class="py-2 pr-4">{{ __('Hubungan') }}</th>
                                <th class="py-2 pr-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($siswa->walis as $w)
                                <tr class="text-sm text-gray-700">
                                    <td class="py-3 pr-4 font-semibold text-gray-900">{{ $w->name }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $w->email }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $w->pivot->hubungan }}</td>
                                    <td class="py-3 pr-4 text-right">
                                        <form method="POST" action="{{ route('siswa.wali.destroy', [$siswa, $w]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-sm font-bold text-red-600 hover:underline" onclick="return confirm('{{ __('Hapus tautan wali ini?') }}')">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-sm text-gray-500">{{ __('Belum ada wali yang ditautkan.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

