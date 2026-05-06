<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Tambah akun') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kode unik per sekolah; pilih tipe sesuai neraca/laba rugi.') }}</p>
            </div>
            <a href="{{ route('keuangan.coa.index') }}" class="btn-nu self-start">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
        <form method="POST" action="{{ route('keuangan.coa.store') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="kode" :value="__('Kode')" />
                <x-text-input id="kode" name="kode" class="mt-2 block w-full font-mono" type="text" :value="old('kode')" required maxlength="32" />
                <x-input-error class="mt-2" :messages="$errors->get('kode')" />
            </div>

            <div>
                <x-input-label for="nama" :value="__('Nama akun')" />
                <x-text-input id="nama" name="nama" class="mt-2 block w-full" type="text" :value="old('nama')" required maxlength="120" />
                <x-input-error class="mt-2" :messages="$errors->get('nama')" />
            </div>

            <div>
                <x-input-label for="tipe" :value="__('Tipe')" />
                <select id="tipe" name="tipe" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25" required>
                    @foreach (\App\Models\AkuntansiAkun::TIPE_OPTIONS as $t)
                        <option value="{{ $t }}" @selected(old('tipe') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
            </div>

            <div class="flex items-center gap-2">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-nu-primary focus:ring-nu-primary/25" @checked(old('is_active', true)) />
                <x-input-label for="is_active" :value="__('Aktif')" class="!mb-0" />
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                <a href="{{ route('keuangan.coa.index') }}" class="btn-nu">{{ __('Batal') }}</a>
                <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
