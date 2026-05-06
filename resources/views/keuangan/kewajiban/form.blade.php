@php
    /** @var \App\Models\KewajibanPembayaran|null $kewajiban */
    $kewajiban = $kewajiban ?? null;
@endphp

<div class="grid gap-4 sm:grid-cols-12">
    @php
        $selectBase = 'w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
        $fieldWrap = 'space-y-2';
        $helper = 'min-h-4 text-xs text-gray-500';
    @endphp

    <div class="sm:col-span-12 lg:col-span-5 {{ $fieldWrap }}">
        <x-input-label for="nama" :value="__('Nama kewajiban')" />
        <x-text-input id="nama" name="nama" class="block w-full" type="text" :value="old('nama', $kewajiban?->nama)" required placeholder="SPP / Uang Gedung / Seragam" />
        <div class="{{ $helper }}"></div>
        <x-input-error :messages="$errors->get('nama')" />
    </div>

    <div class="sm:col-span-6 lg:col-span-2 {{ $fieldWrap }}">
        <x-input-label for="tipe" :value="__('Tipe')" />
        <select id="tipe" name="tipe" class="{{ $selectBase }}" required>
            @foreach (\App\Models\KewajibanPembayaran::TIPE_OPTIONS as $t)
                <option value="{{ $t }}" @selected(old('tipe', $kewajiban?->tipe) === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
        <div class="{{ $helper }}"></div>
        <x-input-error :messages="$errors->get('tipe')" />
    </div>

    <div class="sm:col-span-6 lg:col-span-2 {{ $fieldWrap }}">
        <x-input-label for="nominal_default" :value="__('Nominal default')" />
        <x-text-input id="nominal_default" name="nominal_default" class="block w-full font-mono" type="number" step="0.01" min="0" :value="old('nominal_default', $kewajiban?->nominal_default ?? 0)" required />
        <div class="{{ $helper }}"></div>
        <x-input-error :messages="$errors->get('nominal_default')" />
    </div>

    <div class="sm:col-span-12 lg:col-span-3 {{ $fieldWrap }}">
        <x-input-label for="berlaku_mulai" :value="__('Berlaku mulai')" />
        <x-text-input id="berlaku_mulai" name="berlaku_mulai" class="block w-full font-mono" type="text" :value="old('berlaku_mulai', $kewajiban?->berlaku_mulai)" placeholder="2025-07" />
        <div class="{{ $helper }}">{{ __('Opsional. Format: YYYY-MM') }}</div>
        <x-input-error :messages="$errors->get('berlaku_mulai')" />
    </div>

    <div class="sm:col-span-4 {{ $fieldWrap }}">
        <x-input-label for="batas_hari_bayar" :value="__('Batas bayar (tgl)')" />
        <x-text-input id="batas_hari_bayar" name="batas_hari_bayar" class="block w-full font-mono" type="number" min="1" max="28" :value="old('batas_hari_bayar', $kewajiban?->batas_hari_bayar)" placeholder="15" />
        <div class="{{ $helper }}">{{ __('Opsional. Contoh: 15 berarti tgl 15 tiap bulan') }}</div>
        <x-input-error :messages="$errors->get('batas_hari_bayar')" />
    </div>

    <div class="sm:col-span-4 {{ $fieldWrap }}">
        <x-input-label for="is_active" :value="__('Status')" />
        <label class="inline-flex w-full items-center justify-between gap-3 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-semibold text-gray-700">
            <span>{{ __('Aktif') }}</span>
            <input id="is_active" type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-gray-300 text-nu-primary shadow-sm focus:ring-nu-primary/25" @checked(old('is_active', $kewajiban?->is_active ?? true))>
        </label>
        <div class="{{ $helper }}"></div>
        <x-input-error :messages="$errors->get('is_active')" />
    </div>
</div>

