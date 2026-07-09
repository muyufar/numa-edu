@php
    $siswaAttachErrors = collect($errors->keys())->contains(fn ($k) => str_starts_with((string) $k, 'siswa_ids'));
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Edit kelas') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Perbarui data kelas/rombel.') }}</p>
            </div>
            <a href="{{ route('kelas.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <form method="POST" action="{{ route('kelas.update', $kelas) }}" class="space-y-6">
                @csrf
                @method('PUT')

                @if ($errors->hasAny(['tingkat', 'nama', 'tahun_ajaran', 'wali_kelas_id', 'is_active']))
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ __('Periksa kembali input yang kamu isi.') }}
                    </div>
                @endif

                @include('kelas._form', ['kelas' => $kelas, 'guruOptions' => $guruOptions])

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <a href="{{ route('kelas.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                        {{ __('Simpan perubahan') }}
                    </button>
                </div>
            </form>
        </div>

        @can('update', $kelas)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Siswa di kelas ini') }}</h3>
                <p class="mt-1 text-xs text-gray-500">{{ __('Pilih siswa yang belum memiliki kelas. Untuk membuat siswa baru, gunakan menu Siswa.') }}</p>

                @if (session('status_siswa'))
                    <div class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status_siswa') }}
                    </div>
                @endif

                @if ($siswaAttachErrors)
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ __('Periksa kembali pilihan siswa.') }}
                        <x-input-error :messages="$errors->get('siswa_ids')" class="mt-2" />
                    </div>
                @endif

                @if ($siswasDalamKelas->isEmpty())
                    <p class="mt-4 text-sm text-gray-500">{{ __('Belum ada siswa di kelas ini.') }}</p>
                @else
                    <ul class="mt-4 divide-y divide-gray-100 rounded-xl border border-gray-100">
                        @foreach ($siswasDalamKelas as $s)
                            <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-2.5 text-sm">
                                <span class="font-medium text-gray-900">{{ $s->nama }}</span>
                                <span class="font-mono text-xs text-gray-600">NIS {{ $s->nis }}</span>
                                @can('update', $s)
                                    <a href="{{ route('siswa.edit', $s) }}" class="text-xs font-semibold text-nu-primary hover:underline">{{ __('Profil') }}</a>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                @endif

                @can('create', \App\Models\Siswa::class)
                    <p class="mt-6 text-xs text-gray-500">
                        <a href="{{ route('siswa.create') }}" class="font-semibold text-nu-primary hover:underline">{{ __('Tambah siswa baru') }}</a>
                        — {{ __('data siswa baru tetap di halaman Siswa.') }}
                    </p>
                @endcan

                @if ($siswaTanpaKelas->isEmpty())
                    <p class="mt-4 rounded-lg border border-amber-100 bg-amber-50/80 px-4 py-3 text-sm text-amber-950">
                        {{ __('Tidak ada siswa tanpa kelas di sekolah ini. Buat siswa di menu Siswa, lalu kembali ke sini untuk memasukkannya ke kelas.') }}
                    </p>
                @else
                    <form method="POST" action="{{ route('kelas.siswa.attach', $kelas) }}" class="mt-6 space-y-4 border-t border-gray-100 pt-6">
                        @csrf
                        <p class="text-sm font-semibold text-gray-800">{{ __('Masukkan siswa tanpa kelas ke rombel ini') }}</p>
                        <fieldset class="max-h-56 space-y-2 overflow-y-auto rounded-xl border border-gray-100 p-3">
                            @foreach ($siswaTanpaKelas as $s)
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-gray-50">
                                    <input
                                        type="checkbox"
                                        name="siswa_ids[]"
                                        value="{{ $s->id }}"
                                        class="rounded border-gray-300 text-nu-primary focus:ring-nu-primary/25"
                                        @checked(in_array((string) $s->id, (array) old('siswa_ids', []), true))
                                    />
                                    <span class="text-sm text-gray-800">{{ $s->nama }}</span>
                                    <span class="font-mono text-xs text-gray-500">NIS {{ $s->nis }}</span>
                                </label>
                            @endforeach
                        </fieldset>
                        <x-input-error :messages="$errors->get('siswa_ids')" class="mt-1" />
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-nu-primary/30 bg-nu-primary/5 px-4 py-2.5 text-sm font-semibold text-nu-primary hover:bg-nu-primary/10">
                                {{ __('Masukkan ke kelas') }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @endcan
    </div>
</x-app-layout>
