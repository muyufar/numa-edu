<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Pengaturan presensi') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Atur mode presensi siswa untuk sekolah Anda.') }}</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <div class="mb-6 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                <div class="font-semibold text-gray-900">{{ $sekolah->nama }}</div>
                <div class="mt-0.5 text-xs text-gray-500">NPSN {{ $sekolah->npsn }}</div>
            </div>

            <form method="POST" action="{{ route('pengaturan.presensi.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-900">{{ __('Mode presensi siswa') }}</label>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Menentukan apakah absensi siswa dicatat sekali sehari atau per mata pelajaran/jadwal.') }}</p>

                    <div class="mt-4 space-y-3">
                        @foreach ($modeOptions as $value => $label)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border px-4 py-3 transition {{ old('presensi_siswa_mode', $currentMode) === $value ? 'border-nu-primary bg-nu-primary/5 ring-1 ring-nu-primary/20' : 'border-gray-200 hover:border-gray-300' }}">
                                <input
                                    type="radio"
                                    name="presensi_siswa_mode"
                                    value="{{ $value }}"
                                    class="mt-1 h-4 w-4 border-gray-300 text-nu-primary focus:ring-nu-primary/25"
                                    @checked(old('presensi_siswa_mode', $currentMode) === $value)
                                />
                                <span>
                                    <span class="block text-sm font-semibold text-gray-900">{{ $label }}</span>
                                    <span class="mt-1 block text-xs text-gray-600">
                                        @if ($value === 'harian')
                                            {{ __('Satu catatan kehadiran per siswa per hari. Cocok untuk SD/MI atau sekolah dengan absensi pagi saja.') }}
                                        @else
                                            {{ __('Catatan kehadiran per jadwal mapel (mis. Matematika jam 1, IPA jam 2). Guru mapel wajib pilih jadwal saat input/scan.') }}
                                        @endif
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    @error('presensi_siswa_mode')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <div class="font-semibold">{{ __('Catatan') }}</div>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-xs">
                        <li>{{ __('Presensi guru dan pegawai tetap harian (1x per hari).') }}</li>
                        <li>{{ __('Mode per mapel membutuhkan jadwal pelajaran yang sudah diisi di menu Kurikulum → Jadwal.') }}</li>
                        <li>{{ __('Data presensi lama tetap aman; mode baru berlaku untuk input berikutnya.') }}</li>
                    </ul>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <a href="{{ route('presensi.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="inline-flex items-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Simpan pengaturan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
