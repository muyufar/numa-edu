<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Pengaturan nomor MoU LP Ma’arif') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Cabang') }}: <strong>{{ $cabang->nama }}</strong></p>
            </div>
            <a href="{{ route('pengurus.lembaga-registrations.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Daftar permohonan lembaga') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-sky-100 bg-sky-50/60 p-4 text-sm text-sky-950">
            <p class="font-semibold">{{ __('Cara nomor surat LP otomatis') }}</p>
            <p class="mt-2">{{ __('Atur angka berikutnya dan sufiks. Contoh: angka berikutnya 546 dengan 4 digit dan sufiks «/PC.1/LPM/E.11/V/2026» menghasilkan nomor «0546/PC.1/LPM/E.11/V/2026». Setiap sekolah yang menandatangani MoU akan memakai nomor ini, lalu angka berikutnya naik otomatis (547, 548, …).') }}</p>
        </div>

        @if (auth()->user()->hasRole('super_admin'))
            <form method="GET" action="{{ route('pengurus.lembaga-mou-settings.edit') }}" class="flex flex-wrap items-end gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="min-w-[12rem] flex-1 space-y-1">
                    <x-input-label for="filter_cabang" :value="__('Pilih cabang')" />
                    <select id="filter_cabang" name="cabang_id" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                        @foreach ($cabangs as $c)
                            <option value="{{ $c->id }}" @selected((int) $cabang->id === (int) $c->id)>{{ $c->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex rounded-xl bg-gray-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">{{ __('Tampilkan') }}</button>
            </form>
        @endif

        <form method="POST" action="{{ route('pengurus.lembaga-mou-settings.update') }}" enctype="multipart/form-data" class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm ring-1 ring-black/5">
            @csrf
            @method('PUT')
            @if (auth()->user()->hasRole('super_admin'))
                <input type="hidden" name="cabang_id" value="{{ $cabang->id }}" />
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <x-input-label for="mou_lp_next_sequence" :value="__('Angka urut berikutnya')" />
                    <x-text-input id="mou_lp_next_sequence" name="mou_lp_next_sequence" type="number" min="1" class="block w-full" :value="old('mou_lp_next_sequence', $cabang->mou_lp_next_sequence ?? 1)" required />
                    <x-input-error :messages="$errors->get('mou_lp_next_sequence')" class="mt-1" />
                </div>
                <div class="space-y-2">
                    <x-input-label for="mou_lp_number_digits" :value="__('Jumlah digit depan (zero-pad)')" />
                    <x-text-input id="mou_lp_number_digits" name="mou_lp_number_digits" type="number" min="1" max="8" class="block w-full" :value="old('mou_lp_number_digits', $cabang->mou_lp_number_digits ?? 4)" required />
                    <x-input-error :messages="$errors->get('mou_lp_number_digits')" class="mt-1" />
                </div>
            </div>

            <div class="mt-4 space-y-2">
                <x-input-label for="mou_lp_number_suffix" :value="__('Sufiks setelah angka (termasuk slash depan)')" />
                <x-text-input id="mou_lp_number_suffix" name="mou_lp_number_suffix" type="text" class="block w-full font-mono text-sm" :value="old('mou_lp_number_suffix', $cabang->mou_lp_number_suffix ?? '/PC.1/LPM/E.11/V/2026')" required placeholder="/PC.1/LPM/E.11/V/2026" />
                <x-input-error :messages="$errors->get('mou_lp_number_suffix')" class="mt-1" />
            </div>

            <div class="mt-6 border-t border-gray-100 pt-6">
                <h3 class="text-sm font-bold text-gray-800">{{ __('Blok stempel & tanda tangan di e-sertifikat') }}</h3>
                <p class="mt-1 text-xs text-gray-500">{{ __('Gambar stempel dan tanda tangan Ketua LP ditampilkan langsung pada PDF e-sertifikat yang diunduh lembaga.') }}</p>
            </div>

            <div class="mt-4 space-y-2">
                <x-input-label for="mou_surat_kota" :value="__('Kota surat (contoh: Magelang)')" />
                <x-text-input id="mou_surat_kota" name="mou_surat_kota" type="text" class="block w-full" :value="old('mou_surat_kota', $cabang->mou_surat_kota)" />
            </div>
            <div class="mt-4 space-y-2">
                <x-input-label for="mou_penandatangan_nama" :value="__('Nama Ketua LP (cetak tebal)')" />
                <x-text-input id="mou_penandatangan_nama" name="mou_penandatangan_nama" type="text" class="block w-full" :value="old('mou_penandatangan_nama', $cabang->mou_penandatangan_nama)" />
            </div>
            <div class="mt-4 space-y-2">
                <x-input-label for="mou_penandatangan_jabatan" :value="__('Jabatan (beberapa baris)')" />
                <textarea id="mou_penandatangan_jabatan" name="mou_penandatangan_jabatan" rows="3" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">{{ old('mou_penandatangan_jabatan', $cabang->mou_penandatangan_jabatan) }}</textarea>
            </div>

            <p class="mt-4 text-xs text-gray-600">{{ __('Untuk e-sertifikat: unggah stempel dan tanda tangan sebagai PNG dengan latar transparan agar tampil menyatu (tumpang tindih) seperti dokumen basah.') }}</p>

            <div class="mt-4 space-y-2">
                <x-input-label for="mou_stempel" :value="__('Gambar stempel bundar LP (PNG/JPG, opsional)')" />
                <input id="mou_stempel" name="mou_stempel" type="file" accept="image/*" class="block w-full text-sm text-gray-700" />
                @if ($cabang->mou_stempel_path)
                    <p class="text-xs text-gray-600">{{ __('Stempel saat ini:') }} <a class="font-semibold text-nu-primary underline" href="{{ asset('storage/'.$cabang->mou_stempel_path) }}" target="_blank">{{ __('Lihat') }}</a></p>
                @endif
                <x-input-error :messages="$errors->get('mou_stempel')" class="mt-1" />
            </div>

            <div class="mt-4 space-y-2">
                <x-input-label for="mou_penandatangan_ttd" :value="__('Gambar tanda tangan Ketua LP (PNG/JPG, opsional)')" />
                <input id="mou_penandatangan_ttd" name="mou_penandatangan_ttd" type="file" accept="image/*" class="block w-full text-sm text-gray-700" />
                @if ($cabang->mou_penandatangan_ttd_path)
                    <p class="text-xs text-gray-600">{{ __('Tanda tangan saat ini:') }} <a class="font-semibold text-nu-primary underline" href="{{ asset('storage/'.$cabang->mou_penandatangan_ttd_path) }}" target="_blank">{{ __('Lihat') }}</a></p>
                @endif
                <x-input-error :messages="$errors->get('mou_penandatangan_ttd')" class="mt-1" />
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex rounded-xl bg-nu-primary px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Simpan pengaturan') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
