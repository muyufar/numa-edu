@php use App\Support\SiswaDokumen; @endphp
<div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
    <h3 class="text-sm font-bold text-gray-900">{{ __('Dokumen profil siswa') }}</h3>
    <p class="mt-1 text-xs text-gray-600">{{ __('Unggah ijazah, KK, foto, KTP ortu, KIP, KIA, akta kelahiran, dan piagam.') }}</p>

    <form method="POST" action="{{ route('siswa.dokumen.update', $siswa) }}" enctype="multipart/form-data" class="mt-4 grid gap-4 sm:grid-cols-2">
        @csrf
        @method('PUT')

        @foreach (SiswaDokumen::fields() as $key => $field)
            <div>
                <label class="block text-sm font-semibold text-gray-700">{{ __($field['label']) }}</label>
                <input type="file" name="{{ $key }}" accept="{{ $field['accept'] }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
                @php $path = $siswa->{$field['path']}; @endphp
                @if ($path)
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('File saat ini:') }}
                        <a href="{{ '/storage/'.$path }}" target="_blank" class="font-semibold text-nu-primary hover:underline">{{ $siswa->{$field['name']} ?: __('Lihat') }}</a>
                    </p>
                @endif
                @error($key)
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="sm:col-span-2 flex justify-end">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                {{ __('Simpan dokumen') }}
            </button>
        </div>
    </form>
</div>
