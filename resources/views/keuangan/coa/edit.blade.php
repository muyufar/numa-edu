<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Ubah akun') }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    @if ($coa->isReservedSystemKode())
                        {{ __('Akun bawaan: hanya nama dan status yang dapat diubah.') }}
                    @else
                        {{ __('Terhubung dengan :n baris jurnal.', ['n' => $coa->jurnal_lines_count]) }}
                    @endif
                </p>
            </div>
            <a href="{{ route('keuangan.coa.index') }}" class="btn-nu self-start">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
        <form method="POST" action="{{ route('keuangan.coa.update', $coa) }}" class="space-y-5">
            @csrf
            @method('PUT')

            @if ($coa->isReservedSystemKode())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {{ __('Kode :kode dan tipe dikunci untuk menjaga integrasi pembayaran & pengeluaran.', ['kode' => $coa->kode]) }}
                </div>
                <div>
                    <x-input-label :value="__('Kode')" />
                    <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 font-mono text-sm text-gray-700">{{ $coa->kode }}</div>
                </div>
                <div>
                    <x-input-label :value="__('Tipe')" />
                    <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">{{ $coa->tipe }}</div>
                </div>
            @else
                <div>
                    <x-input-label for="kode" :value="__('Kode')" />
                    <x-text-input id="kode" name="kode" class="mt-2 block w-full font-mono" type="text" :value="old('kode', $coa->kode)" required maxlength="32" />
                    <x-input-error class="mt-2" :messages="$errors->get('kode')" />
                </div>
                <div>
                    <x-input-label for="tipe" :value="__('Tipe')" />
                    <select id="tipe" name="tipe" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25" required>
                        @foreach (\App\Models\AkuntansiAkun::TIPE_OPTIONS as $t)
                            <option value="{{ $t }}" @selected(old('tipe', $coa->tipe) === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
                </div>
            @endif

            <div>
                <x-input-label for="nama" :value="__('Nama akun')" />
                <x-text-input id="nama" name="nama" class="mt-2 block w-full" type="text" :value="old('nama', $coa->nama)" required maxlength="120" />
                <x-input-error class="mt-2" :messages="$errors->get('nama')" />
            </div>

            <div class="flex items-center gap-2">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-nu-primary focus:ring-nu-primary/25" @checked(old('is_active', $coa->is_active)) />
                <x-input-label for="is_active" :value="__('Aktif')" class="!mb-0" />
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                <a href="{{ route('keuangan.coa.index') }}" class="btn-nu">{{ __('Batal') }}</a>
                <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
