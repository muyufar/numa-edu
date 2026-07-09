@php /** @var \App\Models\PerpustakaanPengaturan $pengaturan */ @endphp
@php $field = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20'; @endphp
<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold text-gray-900">{{ __('Pengaturan perpustakaan') }}</h2></x-slot>
    <div class="mx-auto max-w-xl rounded-3xl bg-white p-6 ring-1 ring-black/5">
        @if (session('status'))<div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('perpustakaan.pengaturan.update') }}" class="grid gap-4 sm:grid-cols-2">
            @csrf @method('PUT')
            <div><label class="text-sm font-semibold">{{ __('Maks. peminjaman aktif') }}</label><input type="number" name="max_peminjaman_aktif" value="{{ old('max_peminjaman_aktif', $pengaturan->max_peminjaman_aktif) }}" class="{{ $field }}" required></div>
            <div><label class="text-sm font-semibold">{{ __('Masa pinjam fisik (hari)') }}</label><input type="number" name="masa_pinjam_fisik_hari" value="{{ old('masa_pinjam_fisik_hari', $pengaturan->masa_pinjam_fisik_hari) }}" class="{{ $field }}" required></div>
            <div><label class="text-sm font-semibold">{{ __('Masa pinjam digital (hari)') }}</label><input type="number" name="masa_pinjam_digital_hari" value="{{ old('masa_pinjam_digital_hari', $pengaturan->masa_pinjam_digital_hari) }}" class="{{ $field }}" required></div>
            <div><label class="text-sm font-semibold">{{ __('Denda per hari (Rp)') }}</label><input type="number" name="denda_per_hari" value="{{ old('denda_per_hari', $pengaturan->denda_per_hari) }}" class="{{ $field }}" required></div>
            <div class="sm:col-span-2"><label class="text-sm font-semibold">{{ __('Maks. perpanjangan') }}</label><input type="number" name="max_perpanjangan" value="{{ old('max_perpanjangan', $pengaturan->max_perpanjangan) }}" class="{{ $field }}" required></div>
            <div class="sm:col-span-2"><button class="btn-nu-primary" type="submit">{{ __('Simpan pengaturan') }}</button></div>
        </form>
    </div>
</x-app-layout>
