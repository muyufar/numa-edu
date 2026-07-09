<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Edit home visit') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $row->siswa?->nama }} · {{ $row->tanggal?->format('d M Y') }}</p>
            </div>
            <a href="{{ route('bk.home-visit.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Riwayat') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ __('Periksa kembali input yang kamu isi.') }}
            </div>
        @endif

        @if ($row->fotoUrl())
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Foto dokumentasi') }}</div>
                <div class="mt-3">
                    <a href="{{ $row->fotoUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-block overflow-hidden rounded-xl border border-gray-200">
                        <img src="{{ $row->fotoUrl() }}" alt="{{ $row->foto_name ?? __('Foto home visit') }}" class="max-h-64 w-auto object-cover" />
                    </a>
                    @if ($row->foto_name)
                        <p class="mt-2 text-xs text-gray-500">{{ $row->foto_name }}</p>
                    @endif
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            @if ($siswas->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {{ __('Tidak dapat memuat daftar siswa untuk baris ini.') }}
                </div>
            @else
                <form method="POST" action="{{ route('bk.home-visit.update', $row) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('bk.home-visit._form', ['row' => $row, 'siswas' => $siswas])
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                            {{ __('Simpan perubahan') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>

        @can('update', $row)
            @if (! $row->dilaporkan_kepsek_at)
                <div class="rounded-2xl border border-violet-100 bg-violet-50 p-5 shadow-sm ring-1 ring-violet-100">
                    <h3 class="text-sm font-bold text-violet-900">{{ __('Laporan ke kepala sekolah') }}</h3>
                    <p class="mt-1 text-xs text-violet-800">{{ __('Kirim laporan home visit ini ke kepala sekolah setelah data lengkap.') }}</p>
                    <form method="POST" action="{{ route('bk.home-visit.lapor-kepsek', $row) }}" class="mt-4" onsubmit="return confirm('{{ __('Kirim laporan home visit ke kepala sekolah?') }}')">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-semibold text-violet-800 hover:bg-violet-100">
                            {{ __('Lapor ke kepala sekolah') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ __('Sudah dilaporkan ke kepala sekolah') }} · {{ $row->dilaporkan_kepsek_at->format('d M Y H:i') }}
                </div>
            @endif
        @endcan

        @can('delete', $row)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Zona berbahaya') }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ __('Hapus catatan home visit permanen dari basis data.') }}</p>
                <form method="POST" action="{{ route('bk.home-visit.destroy', $row) }}" class="mt-4" onsubmit="return confirm('{{ __('Hapus catatan home visit ini?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                        {{ __('Hapus catatan') }}
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-app-layout>
