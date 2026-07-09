@php
    $selectedIds = array_map('strval', (array) ($selectedSiswaIds ?? []));
    $visibleIds = $siswas->pluck('id')->map(fn ($id) => (string) $id)->all();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Edit lomba / ajang') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Perbarui data lomba dan kelola peserta.') }}</p>
            </div>
            <a href="{{ route('kesiswaan.lomba.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ __('Periksa kembali input yang kamu isi.') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Peserta') }}</h3>
            <p class="mt-1 text-xs text-gray-600">{{ __('Pilih kelas untuk menampilkan siswa, lalu centang peserta lomba.') }}</p>

            <form method="GET" action="{{ route('kesiswaan.lomba.edit', $lomba_ajang) }}" class="mt-4 grid gap-3 sm:grid-cols-2 sm:items-end rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kelas') }}</label>
                    <select name="kelas_id" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Pilih kelas —') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) $kelasId === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-nu-primary/30 bg-nu-primary/5 px-4 py-2.5 text-sm font-semibold text-nu-primary hover:bg-nu-primary/10">
                        {{ __('Tampilkan siswa') }}
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('kesiswaan.lomba.update', $lomba_ajang) }}" class="mt-6 space-y-6">
                @csrf
                @method('PUT')
                @include('kesiswaan.lomba._form', ['row' => $lomba_ajang])

                <div class="border-t border-gray-100 pt-6">

                    @foreach ($selectedIds as $sid)
                        @if (! in_array($sid, $visibleIds, true))
                            <input type="hidden" name="siswa_ids[]" value="{{ $sid }}" />
                        @endif
                    @endforeach

                    @if ($lomba_ajang->pesertas->isNotEmpty())
                        <div class="mt-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Peserta terdaftar') }} ({{ $lomba_ajang->pesertas->count() }})</p>
                            <ul class="mt-2 flex flex-wrap gap-2">
                                @foreach ($lomba_ajang->pesertas as $peserta)
                                    <li class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs text-gray-700">
                                        {{ $peserta->siswa?->nama }}
                                        @if ($peserta->siswa?->kelas)
                                            <span class="text-gray-500">· {{ $peserta->siswa->kelas->tingkat }} {{ $peserta->siswa->kelas->nama }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($kelasId && $siswas->isNotEmpty())
                        <fieldset class="mt-4 max-h-64 space-y-2 overflow-y-auto rounded-xl border border-gray-100 p-3">
                            @foreach ($siswas as $s)
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-gray-50">
                                    <input
                                        type="checkbox"
                                        name="siswa_ids[]"
                                        value="{{ $s->id }}"
                                        class="rounded border-gray-300 text-nu-primary focus:ring-nu-primary/25"
                                        @checked(in_array((string) $s->id, old('siswa_ids', $selectedIds), true))
                                    />
                                    <span class="text-sm text-gray-800">{{ $s->nama }}</span>
                                    <span class="font-mono text-xs text-gray-500">NIS {{ $s->nis }}</span>
                                </label>
                            @endforeach
                        </fieldset>
                        @error('siswa_ids')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    @elseif ($kelasId && $siswas->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('Tidak ada siswa di kelas ini.') }}</p>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                        {{ __('Simpan perubahan') }}
                    </button>
                </div>
            </form>
        </div>

        @can('delete', $lomba_ajang)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Zona berbahaya') }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ __('Hapus lomba / ajang beserta data pesertanya.') }}</p>
                <form method="POST" action="{{ route('kesiswaan.lomba.destroy', $lomba_ajang) }}" class="mt-4" onsubmit="return confirm('{{ __('Hapus lomba ini?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                        {{ __('Hapus lomba') }}
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-app-layout>
