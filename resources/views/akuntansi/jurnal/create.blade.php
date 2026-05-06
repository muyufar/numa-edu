<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Jurnal manual') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Minimal dua baris; total debit harus sama dengan total kredit.') }}</p>
            </div>
            <a href="{{ route('akuntansi.jurnal.index') }}" class="btn-nu self-start">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-4">
        @if ($errors->has('lines'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                {{ $errors->first('lines') }}
            </div>
        @endif

        <form method="POST" action="{{ route('akuntansi.jurnal.store') }}" class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="tanggal" :value="__('Tanggal')" />
                    <x-text-input id="tanggal" name="tanggal" class="mt-2 block w-full" type="date" :value="old('tanggal', now()->format('Y-m-d'))" required />
                    <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
                </div>
                <div>
                    <x-input-label for="no_bukti" :value="__('No. bukti (opsional)')" />
                    <x-text-input id="no_bukti" name="no_bukti" class="mt-2 block w-full" type="text" :value="old('no_bukti')" maxlength="64" />
                    <x-input-error class="mt-2" :messages="$errors->get('no_bukti')" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="keterangan" :value="__('Keterangan (opsional)')" />
                <x-text-input id="keterangan" name="keterangan" class="mt-2 block w-full" type="text" :value="old('keterangan')" maxlength="500" />
                <x-input-error class="mt-2" :messages="$errors->get('keterangan')" />
            </div>

            <div class="mt-6 border-t border-gray-100 pt-4">
                <div class="text-sm font-bold text-gray-900">{{ __('Baris jurnal') }}</div>
                <p class="mt-1 text-xs text-gray-500">{{ __('Isi nominal di kolom debit atau kredit (salah satu per baris).') }}</p>

                <div class="mt-3 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-3 py-2">{{ __('Akun') }}</th>
                                <th class="px-3 py-2 text-right">{{ __('Debit') }}</th>
                                <th class="px-3 py-2 text-right">{{ __('Kredit') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @for ($i = 0; $i < 8; $i++)
                                <tr>
                                    <td class="px-3 py-2">
                                        <select name="lines[{{ $i }}][akun_id]" class="w-full min-w-[12rem] rounded-lg border-gray-200 text-sm shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                                            <option value="">{{ __('— Pilih akun —') }}</option>
                                            @foreach ($akuns as $a)
                                                <option value="{{ $a->id }}" @selected((string) old('lines.'.$i.'.akun_id') === (string) $a->id)>{{ $a->kode }} — {{ $a->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input name="lines[{{ $i }}][debit]" type="number" step="0.01" min="0" value="{{ old('lines.'.$i.'.debit') }}" class="w-full rounded-lg border-gray-200 text-right text-sm shadow-sm focus:border-nu-primary focus:ring-nu-primary/25" placeholder="0" />
                                    </td>
                                    <td class="px-3 py-2">
                                        <input name="lines[{{ $i }}][kredit]" type="number" step="0.01" min="0" value="{{ old('lines.'.$i.'.kredit') }}" class="w-full rounded-lg border-gray-200 text-right text-sm shadow-sm focus:border-nu-primary focus:ring-nu-primary/25" placeholder="0" />
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t border-gray-100 pt-4">
                <a href="{{ route('akuntansi.jurnal.index') }}" class="btn-nu">{{ __('Batal') }}</a>
                <x-primary-button type="submit">{{ __('Simpan jurnal') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
