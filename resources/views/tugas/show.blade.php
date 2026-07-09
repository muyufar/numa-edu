<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ $tugas->judul }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $tugas->mataPelajaran->nama ?? '-' }}
                    · {{ $tugas->kelas ? ($tugas->kelas->tingkat.' '.$tugas->kelas->nama) : __('Semua kelas') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('tugas.index') }}" class="btn-nu">{{ __('Daftar tugas') }}</a>
                @can('submit', $tugas)
                    <a href="{{ route('tugas.kerjakan', $tugas) }}" class="btn-nu-primary">{{ __('Kerjakan tugas') }}</a>
                @endcan
                @can('update', $tugas)
                    <a href="{{ route('tugas.edit', $tugas) }}" class="btn-nu-primary">{{ __('Edit') }}</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
            @endif

            <div class="overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 pb-4">
                    @if ($tugas->isOverdue())
                        <span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-200">{{ __('Lewat batas') }}</span>
                    @elseif ($tugas->tanggal_batas)
                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">{{ __('Aktif') }}</span>
                    @endif
                    @if (! $tugas->is_published)
                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">{{ __('Draft') }}</span>
                    @endif
                    <span class="inline-flex rounded-full bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200">
                        {{ \App\Models\Tugas::tipeLabel($tugas->tipe) }}
                    </span>
                    <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-800 ring-1 ring-indigo-200">
                        {{ \App\Models\Tugas::jenisSoalLabel($tugas->jenis_soal) }}
                    </span>
                </div>

                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Hari · Jam penugasan') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $tugas->jadwalLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Batas pengumpulan') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $tugas->batasLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Guru pengampu') }}</dt>
                        <dd class="mt-1 text-gray-800">{{ $tugas->guru->nama ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Bobot') }}</dt>
                        <dd class="mt-1 text-gray-800">{{ $tugas->bobot ? $tugas->bobot.' '.__('poin') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Semester') }}</dt>
                        <dd class="mt-1 text-gray-800">{{ $tugas->semester ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tahun ajaran') }}</dt>
                        <dd class="mt-1 font-mono text-gray-800">{{ $tugas->tahun_ajaran ?? '—' }}</dd>
                    </div>
                    @if ($tugas->diunggahOleh)
                        <div class="sm:col-span-2 text-xs text-gray-500">
                            {{ __('Dibuat oleh') }} {{ $tugas->diunggahOleh->name }}
                            · {{ \App\Support\DateTimeFormat::datetime($tugas->created_at) }}
                        </div>
                    @endif
                </dl>
            </div>

            @if ($pengumpulan)
                <div class="overflow-hidden rounded-3xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm ring-1 ring-emerald-100 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-sm font-bold text-emerald-900">{{ __('Pengumpulan Anda') }}</h3>
                        <span class="text-xs font-semibold text-emerald-800">
                            {{ __('Dikumpulkan') }} · {{ \App\Support\DateTimeFormat::datetime($pengumpulan->dikumpulkan_pada) }}
                        </span>
                    </div>
                    @if ($pengumpulan->nilai_otomatis !== null)
                        <p class="mt-3 text-sm text-emerald-900">
                            {{ __('Nilai otomatis') }}: <span class="font-bold">{{ $pengumpulan->nilai_otomatis }}</span>
                            @if ($tugas->bobot)
                                / {{ $tugas->bobot }} {{ __('poin') }}
                            @endif
                        </p>
                    @endif
                    @if ($pengumpulan->jawaban_esai)
                        <div class="mt-4 rounded-2xl border border-emerald-100 bg-white p-4 text-sm whitespace-pre-wrap text-gray-800">{{ $pengumpulan->jawaban_esai }}</div>
                    @endif
                    @if ($pengumpulan->file_path)
                        <p class="mt-3 text-sm text-emerald-900">{{ __('Lampiran') }}: {{ $pengumpulan->file_name }}</p>
                    @endif
                </div>
            @endif

            @if ($tugas->isEsai() && $tugas->bahan_materi && ! $pengumpulan)
                <div class="overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Soal esai') }}</h3>
                    <div class="prose prose-sm mt-4 max-w-none whitespace-pre-wrap text-gray-800">{{ $tugas->bahan_materi }}</div>
                </div>
            @endif

            @if ($tugas->isPilihanGanda() && $tugas->soals->isNotEmpty() && ! $pengumpulan)
                @php $showKunci = \App\Support\PolicyRoles::akademikTim(auth()->user()); @endphp
                <div class="overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-sm font-bold text-gray-900">{{ __('Soal pilihan ganda') }}</h3>
                        <span class="text-xs font-semibold text-gray-500">{{ $tugas->soals->count() }} {{ __('soal') }}</span>
                    </div>
                    <div class="mt-5 space-y-6">
                        @foreach ($tugas->soals as $soal)
                            <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4 sm:p-5">
                                <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Soal') }} {{ $soal->urutan }}</div>
                                <p class="mt-2 whitespace-pre-wrap text-sm font-medium text-gray-900">{{ $soal->pertanyaan }}</p>
                                <ul class="mt-4 space-y-2">
                                    @foreach ($soal->pilihans as $pilihan)
                                        <li class="flex items-start gap-3 rounded-xl border px-3 py-2.5 text-sm {{ $showKunci && $pilihan->is_benar ? 'border-emerald-300 bg-emerald-50 text-emerald-900' : 'border-gray-200 bg-white text-gray-800' }}">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $showKunci && $pilihan->is_benar ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $pilihan->label }}</span>
                                            <span class="pt-0.5">{{ $pilihan->teks }}</span>
                                            @if ($showKunci && $pilihan->is_benar)
                                                <span class="ms-auto shrink-0 text-xs font-semibold text-emerald-700">{{ __('Benar') }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    @unless ($showKunci)
                        <p class="mt-4 text-xs text-gray-500">{{ __('Pilih jawaban yang menurut Anda benar saat mengerjakan tugas.') }}</p>
                    @endunless
                </div>
            @endif

            @if ($tugas->instruksi)
                <div class="overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Instruksi pengerjaan') }}</h3>
                    <div class="mt-3 whitespace-pre-wrap text-sm leading-relaxed text-gray-700">{{ $tugas->instruksi }}</div>
                </div>
            @endif

            @if ($tugas->file_path)
                <div class="overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Lampiran') }}</h3>
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <span class="text-sm text-gray-700">{{ $tugas->file_name }}</span>
                        @php $kb = $tugas->size ? (int) round($tugas->size / 1024) : null; @endphp
                        @if ($kb)
                            <span class="text-xs text-gray-500">({{ number_format($kb) }} KB)</span>
                        @endif
                        <a href="{{ route('tugas.download', $tugas) }}" class="inline-flex items-center rounded-xl bg-nu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-nu-primary-light">
                            {{ __('Unduh lampiran') }}
                        </a>
                    </div>
                </div>
            @endif

            @can('delete', $tugas)
                <form method="POST" action="{{ route('tugas.destroy', $tugas) }}" onsubmit="return confirm('{{ __('Hapus tugas ini?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">{{ __('Hapus tugas') }}</button>
                </form>
            @endcan
        </div>
    </div>
</x-app-layout>
