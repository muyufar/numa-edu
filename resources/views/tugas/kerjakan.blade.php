<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Kerjakan tugas') }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $tugas->judul }}
                    · {{ $tugas->mataPelajaran->nama ?? '-' }}
                </p>
            </div>
            <a href="{{ route('tugas.show', $tugas) }}" class="btn-nu">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ __('Periksa kembali jawaban Anda.') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Batas pengumpulan') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $tugas->batasLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Jenis soal') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ \App\Models\Tugas::jenisSoalLabel($tugas->jenis_soal) }}</dd>
                    </div>
                </dl>

                @if ($tugas->instruksi)
                    <div class="mt-5 rounded-2xl border border-amber-100 bg-amber-50/70 px-4 py-3 text-sm text-amber-950">
                        <div class="text-xs font-bold uppercase tracking-wide text-amber-800">{{ __('Instruksi') }}</div>
                        <div class="mt-1 whitespace-pre-wrap">{{ $tugas->instruksi }}</div>
                    </div>
                @endif

                @if ($tugas->isEsai() && $tugas->bahan_materi)
                    <div class="mt-5 rounded-2xl border border-gray-100 bg-gray-50/70 px-4 py-4">
                        <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Soal esai') }}</div>
                        <div class="prose prose-sm mt-3 max-w-none whitespace-pre-wrap text-gray-800">{{ $tugas->bahan_materi }}</div>
                    </div>
                @endif

                @if ($tugas->file_path)
                    <div class="mt-5 flex flex-wrap items-center gap-3 text-sm">
                        <span class="text-gray-700">{{ $tugas->file_name }}</span>
                        <a href="{{ route('tugas.download', $tugas) }}" class="inline-flex items-center rounded-xl border border-nu-primary/30 bg-nu-primary/5 px-3 py-1.5 text-xs font-semibold text-nu-primary hover:bg-nu-primary/10">
                            {{ __('Unduh lampiran') }}
                        </a>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('tugas.kerjakan.store', $tugas) }}" enctype="multipart/form-data" class="overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                @csrf

                @if ($tugas->isPilihanGanda())
                    <div class="space-y-6">
                        @foreach ($tugas->soals as $soal)
                            <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4 sm:p-5">
                                <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Soal') }} {{ $soal->urutan }}</div>
                                <p class="mt-2 whitespace-pre-wrap text-sm font-medium text-gray-900">{{ $soal->pertanyaan }}</p>
                                <fieldset class="mt-4 space-y-2">
                                    @foreach ($soal->pilihans as $pilihan)
                                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm hover:border-nu-primary/30 hover:bg-nu-primary/5">
                                            <input
                                                type="radio"
                                                name="jawaban[{{ $soal->id }}]"
                                                value="{{ $pilihan->id }}"
                                                class="mt-1 border-gray-300 text-nu-primary focus:ring-nu-primary/25"
                                                @checked((string) old('jawaban.'.$soal->id) === (string) $pilihan->id)
                                                required
                                            />
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">{{ $pilihan->label }}</span>
                                            <span class="pt-0.5 text-gray-800">{{ $pilihan->teks }}</span>
                                        </label>
                                    @endforeach
                                </fieldset>
                                @error('jawaban.'.$soal->id)
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                        @error('jawaban')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">{{ __('Jawaban Anda') }}</label>
                            <textarea
                                name="jawaban_esai"
                                rows="10"
                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
                                required
                                placeholder="{{ __('Tulis jawaban di sini...') }}"
                            >{{ old('jawaban_esai') }}</textarea>
                            @error('jawaban_esai')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">{{ __('Lampiran (opsional)') }}</label>
                            <input type="file" name="file" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-nu-primary/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-nu-primary" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Maks. 10 MB') }}</p>
                            @error('file')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('tugas.show', $tugas) }}" class="btn-nu w-full justify-center sm:w-auto">{{ __('Batal') }}</a>
                    <button type="submit" class="btn-nu-primary w-full justify-center sm:w-auto" onclick="return confirm('{{ __('Kumpulkan tugas ini?') }}')">
                        {{ __('Kumpulkan tugas') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
