<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Input massal nilai') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Isi nilai akhir untuk semua siswa dalam satu kelas dan mapel.') }}</p>
            </div>
            <a href="{{ route('nilai.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
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

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <form method="GET" action="{{ route('nilai.bulk.create') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:items-end">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
                    <select name="kelas_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                        <option value="">{{ __('— Pilih —') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) old('kelas_id', $kelasId) === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">{{ __('Mapel') }}</label>
                    <select name="mata_pelajaran_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                        <option value="">{{ __('— Pilih —') }}</option>
                        @foreach ($mapelOptions as $m)
                            <option value="{{ $m->id }}" {{ (string) old('mata_pelajaran_id', $mapelId) === (string) $m->id ? 'selected' : '' }}>{{ $m->nama }} ({{ $m->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">{{ __('Semester') }}</label>
                    <select name="semester" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                        @foreach (\App\Models\Nilai::SEMESTER_OPTIONS as $s)
                            <option value="{{ $s }}" {{ (string) old('semester', $semester) === (string) $s ? 'selected' : '' }}>
                                {{ $s === '1' ? __('Semester 1 (Ganjil)') : __('Semester 2 (Genap)') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
                    @if ($tahunAjaranOptions->isNotEmpty())
                        <select name="tahun_ajaran" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                            <option value="">{{ __('— Pilih —') }}</option>
                            @foreach ($tahunAjaranOptions as $t)
                                <option value="{{ $t }}" {{ (string) old('tahun_ajaran', $tahunAjaran) === (string) $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran) }}" maxlength="16" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="2025/2026" required />
                    @endif
                </div>
                <div class="sm:col-span-2 lg:col-span-4">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Tampilkan daftar siswa') }}
                    </button>
                </div>
            </form>
        </div>

        @if ($kelasId && $mapelId && $tahunAjaran && $siswas->isNotEmpty())
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <form method="POST" action="{{ route('nilai.bulk.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}" />
                    <input type="hidden" name="mata_pelajaran_id" value="{{ $mapelId }}" />
                    <input type="hidden" name="semester" value="{{ $semester }}" />
                    <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}" />

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
                                    <th class="px-4 py-3">{{ __('Nilai akhir') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($siswas as $s)
                                    @php
                                        $i = $loop->index;
                                        $def = $existing->get($s->id)?->nilai_akhir;
                                        $val = old("nilai.$i.nilai_akhir", $def);
                                    @endphp
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-4 py-3 font-mono text-gray-800">{{ $s->nis }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $s->nama }}</td>
                                        <td class="px-4 py-3">
                                            <input type="hidden" name="nilai[{{ $i }}][siswa_id]" value="{{ $s->id }}" />
                                            <input type="number" name="nilai[{{ $i }}][nilai_akhir]" value="{{ $val }}" step="0.01" min="0" max="100" class="w-full max-w-[8rem] rounded-lg border border-gray-200 bg-white px-2 py-2 font-mono text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
                                            @error("nilai.$i.nilai_akhir")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                            @error("nilai.$i.siswa_id")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end border-t border-gray-100 pt-5">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                            {{ __('Simpan semua') }}
                        </button>
                    </div>
                </form>
            </div>
        @elseif ($kelasId && $mapelId && $tahunAjaran && $siswas->isEmpty())
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ __('Tidak ada siswa di kelas ini.') }}
            </div>
        @endif
    </div>
</x-app-layout>
