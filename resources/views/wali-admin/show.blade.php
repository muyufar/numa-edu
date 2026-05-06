<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Detail wali') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $wali->name }} · <span class="font-semibold text-gray-900">{{ $wali->email }}</span></p>
            </div>
            <a href="{{ route('wali-admin.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <div class="text-sm font-extrabold text-gray-900">{{ __('Profil wali') }}</div>
            <p class="mt-1 text-xs text-gray-500">{{ __('Perbarui nama, email, dan kontak wali.') }}</p>

            <form method="POST" action="{{ route('wali-admin.update', $wali) }}" class="mt-4 grid gap-4 sm:grid-cols-12">
                @csrf
                @method('PUT')

                <div class="sm:col-span-4">
                    <x-input-label for="wali_name" :value="__('Nama')" />
                    <x-text-input id="wali_name" name="name" class="mt-2 block w-full" type="text" :value="old('name', $wali->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="sm:col-span-4">
                    <x-input-label for="wali_email" :value="__('Email')" />
                    <x-text-input id="wali_email" name="email" class="mt-2 block w-full" type="email" :value="old('email', $wali->email)" required autocomplete="off" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="sm:col-span-3">
                    <x-input-label for="wali_phone" :value="__('No. HP')" />
                    <x-text-input id="wali_phone" name="phone" class="mt-2 block w-full" type="text" :value="old('phone', $wali->phone)" autocomplete="tel" placeholder="08xxxxxxxxxx" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div class="sm:col-span-1 flex items-end">
                    <x-primary-button class="w-full" type="submit">{{ __('Simpan') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <div class="text-sm font-extrabold text-gray-900">{{ __('Reset password') }}</div>
            <p class="mt-1 text-xs text-gray-500">{{ __('Atur ulang password akun wali.') }}</p>

            <form method="POST" action="{{ route('wali-admin.reset-password', $wali) }}" class="mt-4 grid gap-4 sm:grid-cols-12">
                @csrf

                <div class="sm:col-span-4">
                    <x-input-label for="reset_password" :value="__('Password baru')" />
                    <x-text-input id="reset_password" name="password" class="mt-2 block w-full" type="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="sm:col-span-4">
                    <x-input-label for="reset_password_confirmation" :value="__('Konfirmasi')" />
                    <x-text-input id="reset_password_confirmation" name="password_confirmation" class="mt-2 block w-full" type="password" required autocomplete="new-password" />
                </div>
                <div class="sm:col-span-4 flex items-end">
                    <x-primary-button type="submit">{{ __('Reset password') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <div class="text-sm font-extrabold text-gray-900">{{ __('Tautkan anak') }}</div>
            <p class="mt-1 text-xs text-gray-500">{{ __('Tautkan wali ini ke siswa yang ada di sekolah yang sama.') }}</p>

            <form method="POST" action="{{ route('wali-admin.attach-siswa', $wali) }}" class="mt-4 grid gap-4 sm:grid-cols-12">
                @csrf

                <div class="sm:col-span-6">
                    <x-input-label for="siswa_id" :value="__('Pilih siswa')" />
                    <select id="siswa_id" name="siswa_id" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25" required>
                        <option value="" disabled @selected(old('siswa_id') === null || old('siswa_id') === '')>{{ __('Pilih siswa') }}</option>
                        @foreach ($siswaOptions as $s)
                            <option value="{{ $s['id'] }}" @selected((string) old('siswa_id') === (string) $s['id'])>{{ $s['nama'] }} ({{ $s['nis'] }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('siswa_id')" class="mt-2" />
                </div>

                <div class="sm:col-span-4">
                    <x-input-label for="hubungan" :value="__('Hubungan')" />
                    <x-text-input id="hubungan" name="hubungan" class="mt-2 block w-full" type="text" :value="old('hubungan', 'orang_tua')" required placeholder="orang_tua" />
                    <x-input-error :messages="$errors->get('hubungan')" class="mt-2" />
                </div>

                <div class="sm:col-span-2 flex items-end">
                    <x-primary-button class="w-full" type="submit">{{ __('Tautkan') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <div class="text-sm font-extrabold text-gray-900">{{ __('Daftar anak') }}</div>
            <p class="mt-1 text-xs text-gray-500">{{ __('Berikut siswa yang tertaut ke akun wali ini.') }}</p>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                            <th class="px-5 py-3">{{ __('Nama siswa') }}</th>
                            <th class="px-5 py-3">{{ __('NIS') }}</th>
                            <th class="px-5 py-3">{{ __('Kelas') }}</th>
                            <th class="px-5 py-3">{{ __('Hubungan') }}</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($wali->waliSiswas as $s)
                            <tr class="text-sm text-gray-700">
                                <td class="px-5 py-3 font-semibold text-gray-900">{{ $s->nama }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $s->nis }}</td>
                                <td class="px-5 py-3 text-gray-600">
                                    @if ($s->kelas)
                                        {{ $s->kelas->tingkat }} {{ $s->kelas->nama }} · {{ $s->kelas->tahun_ajaran }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $s->pivot->hubungan }}</td>
                                <td class="px-5 py-3 text-right">
                                    <form method="POST" action="{{ route('siswa.wali.destroy', [$s, $wali]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm font-bold text-red-600 hover:underline" onclick="return confirm('{{ __('Hapus tautan wali untuk siswa ini?') }}')">
                                            {{ __('Lepas') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">
                                    {{ __('Belum ada siswa yang tertaut.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

