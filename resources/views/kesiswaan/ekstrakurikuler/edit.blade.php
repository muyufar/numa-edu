@php
    $selectedIds = array_map('strval', (array) ($selectedSiswaIds ?? []));
    $visibleIds = $siswas->pluck('id')->map(fn ($id) => (string) $id)->all();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Edit ekstrakurikuler') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Perbarui data ekskul, anggota, dan kegiatan.') }}</p>
            </div>
            <a href="{{ route('kesiswaan.ekstrakurikuler.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
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
            <form method="GET" action="{{ route('kesiswaan.ekstrakurikuler.edit', $row) }}" class="grid gap-3 sm:grid-cols-2 sm:items-end rounded-xl border border-gray-100 bg-gray-50/50 p-4">
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

            <form method="POST" action="{{ route('kesiswaan.ekstrakurikuler.update', $row) }}" class="mt-6 space-y-6">
                @csrf
                @method('PUT')
                @include('kesiswaan.ekstrakurikuler._form', ['row' => $row, 'guruOptions' => $guruOptions])

                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Anggota') }}</h3>
                    <p class="mt-1 text-xs text-gray-600">{{ __('Pilih kelas di bawah, centang siswa, lalu simpan perubahan.') }}</p>

                    @foreach ($selectedIds as $sid)
                        @if (! in_array($sid, $visibleIds, true))
                            <input type="hidden" name="siswa_ids[]" value="{{ $sid }}" />
                        @endif
                    @endforeach

                    @if ($row->anggotas->isNotEmpty())
                        <div class="mt-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Anggota terdaftar') }} ({{ $row->anggotas->count() }})</p>
                            <ul class="mt-2 flex flex-wrap gap-2">
                                @foreach ($row->anggotas as $anggota)
                                    <li class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs text-gray-700">
                                        {{ $anggota->siswa?->nama }}
                                        <span class="font-mono text-gray-500">NIS {{ $anggota->siswa?->nis }}</span>
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
                        <p class="mt-4 text-sm text-gray-500">{{ __('Tidak ada siswa di kelas ini.') }}</p>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                        {{ __('Simpan perubahan') }}
                    </button>
                </div>
            </form>
        </div>

        @can('update', $row)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Kegiatan ekskul') }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ __('Catat kegiatan latihan, pertandingan, atau pertemuan ekskul.') }}</p>

                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">{{ __('Tanggal') }}</th>
                                <th class="px-4 py-3">{{ __('Judul') }}</th>
                                <th class="px-4 py-3 hidden md:table-cell">{{ __('Laporan') }}</th>
                                <th class="px-4 py-3 hidden lg:table-cell">{{ __('Dicatat') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($row->kegiatans as $kegiatan)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $kegiatan->tanggal?->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $kegiatan->judul }}</td>
                                    <td class="px-4 py-3 text-gray-600 hidden md:table-cell max-w-xs truncate" title="{{ $kegiatan->laporan }}">{{ $kegiatan->laporan ? \Illuminate\Support\Str::limit($kegiatan->laporan, 80) : '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500 hidden lg:table-cell">{{ $kegiatan->dicatatOleh?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('kesiswaan.ekstrakurikuler.kegiatan.destroy', [$row, $kegiatan]) }}" onsubmit="return confirm('{{ __('Hapus kegiatan ini?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('Belum ada kegiatan.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <form method="POST" action="{{ route('kesiswaan.ekstrakurikuler.kegiatan.store', $row) }}" class="mt-6 space-y-4 border-t border-gray-100 pt-6">
                    @csrf
                    <p class="text-sm font-semibold text-gray-800">{{ __('Tambah kegiatan') }}</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal') }}</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal') }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
                            @error('tanggal')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">{{ __('Judul') }}</label>
                            <input type="text" name="judul" value="{{ old('judul') }}" maxlength="160" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
                            @error('judul')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">{{ __('Laporan') }}</label>
                        <textarea name="laporan" rows="3" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional') }}">{{ old('laporan') }}</textarea>
                        @error('laporan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-nu-primary/30 bg-nu-primary/5 px-4 py-2.5 text-sm font-semibold text-nu-primary hover:bg-nu-primary/10">
                            {{ __('Tambah kegiatan') }}
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        @can('delete', $row)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Zona berbahaya') }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ __('Hapus ekstrakurikuler beserta anggota dan kegiatannya.') }}</p>
                <form method="POST" action="{{ route('kesiswaan.ekstrakurikuler.destroy', $row) }}" class="mt-4" onsubmit="return confirm('{{ __('Hapus ekskul ini?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                        {{ __('Hapus ekskul') }}
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-app-layout>
