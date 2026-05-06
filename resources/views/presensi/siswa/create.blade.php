<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Input presensi siswa') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Pilih kelas dan tanggal, lalu isi status per siswa.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('presensi.siswa.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Riwayat') }}
                </a>
                <a href="{{ route('presensi.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Absensi') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <form method="GET" action="{{ route('presensi.siswa.create') }}" class="grid gap-4 sm:grid-cols-3 sm:items-end">
                <div class="sm:col-span-1">
                    <label class="block text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
                    <select name="kelas_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                        <option value="">{{ __('— Pilih kelas —') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) old('kelas_id', $kelasId) === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}{{ $k->is_active ? '' : ' (nonaktif)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal') }}</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $tanggal) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
                </div>
                <div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light sm:w-auto">
                        {{ __('Tampilkan daftar') }}
                    </button>
                </div>
            </form>
        </div>

        @if ($kelasId && $siswas->isNotEmpty())
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <form method="POST" action="{{ route('presensi.siswa.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}" />
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}" />

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ __('Periksa kembali input yang kamu isi.') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">{{ __('NIS') }}</th>
                                    <th class="px-4 py-3">{{ __('Nama') }}</th>
                                    <th class="px-4 py-3">{{ __('Status') }}</th>
                                    <th class="px-4 py-3">{{ __('Keterangan') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($siswas as $s)
                                    @php
                                        $i = $loop->index;
                                        $defStatus = $existing->get($s->id)?->status ?? 'hadir';
                                        $statusVal = old("presensi.$i.status", $defStatus);
                                        $ketVal = old("presensi.$i.keterangan", $existing->get($s->id)?->keterangan);
                                    @endphp
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-4 py-3 font-mono text-gray-800">{{ $s->nis }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $s->nama }}</td>
                                        <td class="px-4 py-3">
                                            <input type="hidden" name="presensi[{{ $i }}][siswa_id]" value="{{ $s->id }}" />
                                            <select name="presensi[{{ $i }}][status]" class="w-full min-w-[8rem] rounded-lg border border-gray-200 bg-white px-2 py-2 text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                                                @foreach (\App\Models\PresensiSiswa::STATUS_OPTIONS as $st)
                                                    <option value="{{ $st }}" {{ $statusVal === $st ? 'selected' : '' }}>
                                                        {{ match ($st) {
                                                            'hadir' => __('Hadir'),
                                                            'izin' => __('Izin'),
                                                            'sakit' => __('Sakit'),
                                                            'alpa' => __('Alpa'),
                                                            default => $st,
                                                        } }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("presensi.$i.status")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                            @error("presensi.$i.siswa_id")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="presensi[{{ $i }}][keterangan]" value="{{ $ketVal }}" maxlength="255" class="w-full min-w-[10rem] rounded-lg border border-gray-200 bg-white px-2 py-2 text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="—" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                            {{ __('Simpan presensi') }}
                        </button>
                    </div>
                </form>
            </div>
        @elseif ($kelasId && $siswas->isEmpty())
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ __('Tidak ada siswa di kelas ini. Tambahkan siswa di master terlebih dahulu.') }}
            </div>
        @endif
    </div>
</x-app-layout>
