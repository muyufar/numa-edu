@php /** @var \App\Models\PerpustakaanBuku $buku */ @endphp
@php $field = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20'; @endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="text-sm font-semibold text-gray-700">{{ __('Judul') }}</label>
        <input name="judul" value="{{ old('judul', $buku->judul) }}" class="{{ $field }}" required>
        @error('judul')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Pengarang') }}</label>
        <input name="pengarang" value="{{ old('pengarang', $buku->pengarang) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Penerbit') }}</label>
        <input name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('ISBN') }}</label>
        <input name="isbn" value="{{ old('isbn', $buku->isbn) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Tahun terbit') }}</label>
        <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Kategori') }}</label>
        <select name="perpustakaan_kategori_id" class="{{ $field }}">
            <option value="">{{ __('— Tanpa kategori —') }}</option>
            @foreach ($kategoriOptions as $k)
                <option value="{{ $k->id }}" @selected((string) old('perpustakaan_kategori_id', $buku->perpustakaan_kategori_id) === (string) $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Tipe koleksi') }}</label>
        <select name="tipe" class="{{ $field }}" required>
            @foreach (\App\Models\PerpustakaanBuku::TIPE_OPTIONS as $t)
                <option value="{{ $t }}" @selected(old('tipe', $buku->tipe) === $t)>{{ (new \App\Models\PerpustakaanBuku(['tipe' => $t]))->labelTipe() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Jumlah eksemplar (fisik)') }}</label>
        <input type="number" min="0" name="jumlah_eksemplar" value="{{ old('jumlah_eksemplar', $buku->jumlah_eksemplar) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Rak / lokasi') }}</label>
        <input name="rak_lokasi" value="{{ old('rak_lokasi', $buku->rak_lokasi) }}" class="{{ $field }}" placeholder="A-12">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Bahasa') }}</label>
        <input name="bahasa" value="{{ old('bahasa', $buku->bahasa ?: 'id') }}" class="{{ $field }}">
    </div>
    <div class="sm:col-span-2">
        <label class="text-sm font-semibold text-gray-700">{{ __('Sinopsis') }}</label>
        <textarea name="sinopsis" rows="4" class="{{ $field }}">{{ old('sinopsis', $buku->sinopsis) }}</textarea>
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('Cover buku') }}</label>
        <input type="file" name="cover" accept="image/*" class="{{ $field }}">
        @if ($buku->coverUrl())
            <p class="mt-2 text-xs text-gray-500">{{ __('Cover saat ini tersedia') }}</p>
            <img src="{{ $buku->coverUrl() }}" alt="{{ $buku->judul }}" class="mt-2 max-h-40 rounded-xl border border-gray-100 object-contain">
        @endif
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700">{{ __('File PDF (digital)') }}</label>
        <input type="file" name="file" accept=".pdf,application/pdf" class="{{ $field }}">
        @error('file')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
        @if ($buku->file_name)
            <p class="mt-1 text-xs text-gray-500">{{ $buku->file_name }}</p>
        @endif
    </div>
    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $buku->is_active)) class="rounded border-gray-300 text-nu-primary">
            {{ __('Aktif di katalog') }}
        </label>
    </div>
</div>
