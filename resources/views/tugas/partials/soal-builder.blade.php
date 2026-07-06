@php
    $jenisSoal = old('jenis_soal', $tugas?->jenis_soal ?? 'esai');
    $soalInitial = old('soal');

    if ($soalInitial === null && $tugas?->isPilihanGanda()) {
        $tugas->loadMissing('soals.pilihans');
        $soalInitial = $tugas->soals->map(function ($s) {
            $benarIndex = $s->pilihans->search(fn ($p) => $p->is_benar);

            return [
                'pertanyaan' => $s->pertanyaan,
                'jawaban_benar' => $benarIndex === false ? 0 : $benarIndex,
                'pilihan' => $s->pilihans->map(fn ($p) => ['teks' => $p->teks])->values()->all(),
            ];
        })->values()->all();
    }

    if (! is_array($soalInitial)) {
        $soalInitial = [];
    }
@endphp

<div
    x-data="tugasSoalBuilder(@js($jenisSoal), @js($soalInitial))"
    class="space-y-4"
>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Jenis soal') }}</label>
        <div class="mt-2 flex flex-wrap gap-2">
            @foreach (\App\Models\Tugas::JENIS_SOAL_OPTIONS as $jenis)
                <label class="cursor-pointer">
                    <input
                        type="radio"
                        name="jenis_soal"
                        value="{{ $jenis }}"
                        class="peer sr-only"
                        x-model="jenisSoal"
                        @change="setJenis(@js($jenis))"
                    >
                    <span class="inline-flex rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition peer-checked:border-nu-primary peer-checked:bg-nu-primary/10 peer-checked:text-nu-primary hover:bg-gray-50">
                        {{ \App\Models\Tugas::jenisSoalLabel($jenis) }}
                    </span>
                </label>
            @endforeach
        </div>
        @error('jenis_soal')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div x-show="jenisSoal === 'esai'" x-cloak>
        <label class="text-sm font-semibold text-gray-700">{{ __('Materi atau soal esai') }}</label>
        <textarea name="bahan_materi" rows="8" class="{{ $field }}" placeholder="{{ __('Tulis pertanyaan esai, studi kasus, atau ringkasan materi...') }}">{{ old('bahan_materi', $tugas?->bahan_materi ?? '') }}</textarea>
        @error('bahan_materi')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div x-show="jenisSoal === 'pilihan_ganda'" x-cloak class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-gray-600">{{ __('Buat soal pilihan ganda. Tandai lingkaran hijau pada jawaban yang benar.') }}</p>
            <button type="button" @click="addSoal()" class="inline-flex items-center rounded-xl border border-nu-primary/30 bg-nu-primary/5 px-3 py-2 text-xs font-bold text-nu-primary hover:bg-nu-primary/10">
                + {{ __('Tambah soal') }}
            </button>
        </div>

        @error('soal')<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>@enderror

        <template x-for="(item, si) in soal" :key="si">
            <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-4 sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-xs font-bold uppercase tracking-wide text-gray-500" x-text="'{{ __('Soal') }} ' + (si + 1)"></div>
                    <button type="button" @click="removeSoal(si)" x-show="soal.length > 1" class="text-xs font-semibold text-red-600 hover:underline">{{ __('Hapus soal') }}</button>
                </div>

                <div class="mt-3">
                    <label class="text-sm font-semibold text-gray-700">{{ __('Pertanyaan') }}</label>
                    <textarea
                        class="{{ $field }}"
                        rows="3"
                        x-model="item.pertanyaan"
                        :name="'soal[' + si + '][pertanyaan]'"
                        placeholder="{{ __('Tulis pertanyaan di sini...') }}"
                        required
                    ></textarea>
                </div>

                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Pilihan jawaban') }}</div>
                        <button type="button" @click="addPilihan(si)" x-show="item.pilihan.length < 6" class="text-xs font-semibold text-nu-primary hover:underline">+ {{ __('Tambah pilihan') }}</button>
                    </div>
                    <input type="hidden" :name="'soal[' + si + '][jawaban_benar]'" :value="item.jawaban_benar">

                    <template x-for="(pilihan, pi) in item.pilihan" :key="pi">
                        <div class="flex items-start gap-2">
                            <button
                                type="button"
                                class="mt-2.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold transition"
                                :class="item.jawaban_benar === pi ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-gray-300 bg-white text-gray-500 hover:border-emerald-400'"
                                :title="'{{ __('Jawaban benar') }}'"
                                @click="setBenar(si, pi)"
                                x-text="labelFor(pi)"
                            ></button>
                            <input
                                type="text"
                                class="{{ $field }} flex-1"
                                x-model="pilihan.teks"
                                :name="'soal[' + si + '][pilihan][' + pi + '][teks]'"
                                :placeholder="'{{ __('Pilihan') }} ' + labelFor(pi)"
                                required
                            >
                            <button type="button" @click="removePilihan(si, pi)" x-show="item.pilihan.length > 2" class="mt-2.5 shrink-0 text-xs font-semibold text-gray-400 hover:text-red-600">✕</button>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>
