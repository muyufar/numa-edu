@props(['initial' => []])

@php
    $wilayahKode = static function ($v): ?string {
        if ($v === null || $v === '') {
            return null;
        }

        return (string) $v;
    };
    $wilayahAlamatInitial = array_merge($initial, [
        'base' => rtrim((string) url('ref/wilayah'), '/'),
        'kode_provinsi' => $wilayahKode(data_get($initial, 'kode_provinsi')),
        'kode_kabupaten' => $wilayahKode(data_get($initial, 'kode_kabupaten')),
        'kode_kecamatan' => $wilayahKode(data_get($initial, 'kode_kecamatan')),
        'kode_kelurahan' => $wilayahKode(data_get($initial, 'kode_kelurahan')),
    ]);
@endphp

<div x-data="wilayahAlamat(@js($wilayahAlamatInitial))" class="space-y-4">
    <p x-show="err" x-cloak class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800" x-text="err"></p>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="space-y-2 sm:col-span-2">
            <x-input-label for="wilayah_prov" :value="__('Provinsi')" />
            <select
                id="wilayah_prov"
                name="kode_provinsi"
                x-model="kodeProv"
                @change="onProvChange()"
                class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
            >
                <option value="">{{ __('Pilih provinsi…') }}</option>
                <template x-for="p in provinces" :key="p.code">
                    <option :value="p.code" x-text="p.name"></option>
                </template>
            </select>
            <input type="hidden" name="nama_provinsi" :value="namaProv" />
            <x-input-error :messages="$errors->get('kode_provinsi')" class="mt-1" />
        </div>

        <div class="space-y-2 sm:col-span-2">
            <x-input-label for="wilayah_kab" :value="__('Kabupaten / Kota')" />
            <select
                id="wilayah_kab"
                name="kode_kabupaten"
                x-model="kodeKab"
                @change="onKabChange()"
                class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                :disabled="!kodeProv"
            >
                <option value="">{{ __('Pilih kabupaten/kota…') }}</option>
                <template x-for="p in regencies" :key="p.code">
                    <option :value="p.code" x-text="p.name"></option>
                </template>
            </select>
            <input type="hidden" name="nama_kabupaten" :value="namaKab" />
            <x-input-error :messages="$errors->get('kode_kabupaten')" class="mt-1" />
        </div>

        <div class="space-y-2 sm:col-span-2">
            <x-input-label for="wilayah_kec" :value="__('Kecamatan')" />
            <select
                id="wilayah_kec"
                name="kode_kecamatan"
                x-model="kodeKec"
                @change="onKecChange()"
                class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                :disabled="!kodeKab"
            >
                <option value="">{{ __('Pilih kecamatan…') }}</option>
                <template x-for="p in districts" :key="p.code">
                    <option :value="p.code" x-text="p.name"></option>
                </template>
            </select>
            <input type="hidden" name="nama_kecamatan" :value="namaKec" />
            <x-input-error :messages="$errors->get('kode_kecamatan')" class="mt-1" />
        </div>

        <div class="space-y-2 sm:col-span-2">
            <x-input-label for="wilayah_kel" :value="__('Kelurahan / Desa')" />
            <select
                id="wilayah_kel"
                name="kode_kelurahan"
                x-model="kodeKel"
                @change="onKelChange()"
                class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
                :disabled="!kodeKec"
            >
                <option value="">{{ __('Pilih kelurahan/desa…') }}</option>
                <template x-for="p in villages" :key="p.code">
                    <option :value="p.code" x-text="p.name"></option>
                </template>
            </select>
            <input type="hidden" name="nama_kelurahan" :value="namaKel" />
            <x-input-error :messages="$errors->get('kode_kelurahan')" class="mt-1" />
        </div>

        <div class="space-y-2 sm:col-span-2">
            <x-input-label for="alamat_dusun" :value="__('Alamat dusun / RT-RW / gang (manual)')" />
            <textarea
                id="alamat_dusun"
                name="alamat_dusun"
                rows="2"
                class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25"
            >{{ data_get($initial, 'alamat_dusun', '') }}</textarea>
            <p class="text-xs text-gray-500">{{ __('Diisi manual, misalnya nama dusun, RT/RW, nomor rumah, atau patokan.') }}</p>
            <x-input-error :messages="$errors->get('alamat_dusun')" class="mt-1" />
        </div>
    </div>
</div>
