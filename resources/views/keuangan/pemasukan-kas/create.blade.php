<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Catat pemasukan kas') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Jurnal: debit kas, kredit akun pendapatan. Untuk pembayaran siswa gunakan Proses pembayaran.') }}</p>
            </div>
            <a href="{{ route('keuangan.pemasukan-kas.index') }}" class="btn-nu self-start">{{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
        <form method="POST" action="{{ route('keuangan.pemasukan-kas.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="tanggal" :value="__('Tanggal')" />
                <x-text-input id="tanggal" name="tanggal" class="mt-2 block w-full" type="date" :value="old('tanggal', now()->format('Y-m-d'))" required />
                <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
            </div>

            <div>
                <x-input-label for="jumlah" :value="__('Jumlah (Rp)')" />
                <x-text-input id="jumlah" name="jumlah" class="mt-2 block w-full" type="number" step="0.01" min="0.01" :value="old('jumlah')" required />
                <x-input-error class="mt-2" :messages="$errors->get('jumlah')" />
            </div>

            <div>
                <x-input-label for="keterangan" :value="__('Keterangan')" />
                <textarea id="keterangan" name="keterangan" rows="3" class="mt-2 w-full rounded-xl border-gray-200 shadow-sm focus:border-nu-primary focus:ring-nu-primary/25" required>{{ old('keterangan') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('keterangan')" />
            </div>

            <div>
                <x-input-label for="no_bukti" :value="__('No. bukti (opsional)')" />
                <x-text-input id="no_bukti" name="no_bukti" class="mt-2 block w-full" type="text" :value="old('no_bukti')" maxlength="64" />
                <x-input-error class="mt-2" :messages="$errors->get('no_bukti')" />
            </div>

            <div>
                <x-input-label for="akun_pendapatan_id" :value="__('Akun pendapatan')" />
                <select id="akun_pendapatan_id" name="akun_pendapatan_id" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                    <option value="">{{ __('Default (Pendapatan SPP / 401)') }}</option>
                    @foreach ($akunPendapatan as $a)
                        <option value="{{ $a->id }}" @selected((string) old('akun_pendapatan_id') === (string) $a->id)>{{ $a->kode }} — {{ $a->nama }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">{{ __('Untuk hibah/sewa, buat akun pendapatan di COA lalu pilih di sini.') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('akun_pendapatan_id')" />
            </div>

            <div>
                <x-input-label for="bukti_nota" :value="__('Bukti nota / kwitansi (opsional)')" />
                <input id="bukti_nota" name="bukti_nota" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-nu-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-nu-primary hover:file:bg-nu-primary/15" />
                <p class="mt-1 text-xs text-gray-500">{{ __('PDF atau foto (JPG/PNG/WebP), maks. 5 MB.') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('bukti_nota')" />
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                <a href="{{ route('keuangan.pemasukan-kas.index') }}" class="btn-nu">{{ __('Batal') }}</a>
                <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
