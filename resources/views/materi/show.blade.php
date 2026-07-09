<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200">
                        {{ $materi_ajar->labelJenis() }}
                    </span>
                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-100">
                        {{ $materi_ajar->labelStatusPublikasi() }}
                    </span>
                    <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-800 ring-1 ring-sky-100">
                        {{ $materi_ajar->labelStatusPenggunaan() }}
                    </span>
                </div>
                <h2 class="mt-2 text-xl font-extrabold text-gray-900">{{ $materi_ajar->judul }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $materi_ajar->mataPelajaran?->nama }}
                    @if ($materi_ajar->kelas)
                        · {{ $materi_ajar->kelas->tingkat }} {{ $materi_ajar->kelas->nama }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('materi.index') }}" class="btn-nu">{{ __('Kembali') }}</a>
                @can('view', $materi_ajar)
                    @if ($materi_ajar->isPdf())
                        <a href="{{ route('materi.preview', $materi_ajar) }}" target="_blank" rel="noopener" class="btn-nu-primary">{{ __('Buka PDF') }}</a>
                    @endif
                    @if ($materi_ajar->file_path)
                        <a href="{{ route('materi.download', $materi_ajar) }}" class="btn-nu">{{ __('Unduh berkas') }}</a>
                    @endif
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @can('view', $materi_ajar)
            @if ($materi_ajar->isPdf())
                <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5" id="baca-pdf">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                        <h3 class="text-sm font-bold text-gray-900">{{ __('Baca PDF') }}</h3>
                        <span class="text-xs text-gray-500">{{ __('Pratinjau langsung di sistem') }}</span>
                    </div>
                    <iframe
                        src="{{ route('materi.preview', $materi_ajar) }}"
                        title="{{ $materi_ajar->judul }}"
                        class="block w-full border-0 bg-gray-100"
                        style="height: min(1200px, calc(100vh - 8rem)); min-height: 900px;"
                        loading="lazy"
                    ></iframe>
                </div>
            @endif
        @endcan

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                @if ($materi_ajar->isModulMerdeka())
                    @include('materi._show_modul_merdeka', ['materi_ajar' => $materi_ajar])
                @elseif ($materi_ajar->hasKontenTerstruktur())
                    @include('materi._show_konten_terstruktur', ['materi_ajar' => $materi_ajar])
                @else
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Detail perangkat ajar') }}</h3>
                    <dl class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Guru') }}</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->guru?->nama ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tahun ajaran') }}</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->tahun_ajaran ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Semester') }}</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->semester ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Pertemuan ke-') }}</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->pertemuan_ke ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tanggal') }}</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->tanggal?->format('d M Y') ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Berkas') }}</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->file_name }}</dd>
                            @php $kb = $materi_ajar->size ? (int) round($materi_ajar->size / 1024) : null; @endphp
                            <dd class="text-xs text-gray-500">{{ $kb ? number_format($kb).' KB' : '' }}</dd>
                        </div>
                    </dl>

                    @if ($materi_ajar->deskripsi)
                        <div class="mt-6 border-t border-gray-100 pt-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Deskripsi') }}</h4>
                            <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $materi_ajar->deskripsi }}</p>
                        </div>
                    @endif
                </div>
                @endif

                <div class="rounded-3xl bg-gray-50 p-6 shadow-sm ring-1 ring-black/5">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Riwayat arsip') }}</h3>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">{{ __('Diunggah') }}</dt>
                            <dd class="font-semibold text-gray-900">
                                {{ $materi_ajar->created_at?->format('d M Y H:i') }}
                                @if ($materi_ajar->diunggahOleh) · {{ $materi_ajar->diunggahOleh->name }} @endif
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">{{ __('Dipublikasi') }}</dt>
                            <dd class="font-semibold text-gray-900">{{ $materi_ajar->dipublikasi_pada?->format('d M Y H:i') ?: '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">{{ __('Diarsipkan') }}</dt>
                            <dd class="font-semibold text-gray-900">{{ $materi_ajar->diarsipkan_pada?->format('d M Y H:i') ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @can('update', $materi_ajar)
                <div class="space-y-4">
                    <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                        <h3 class="text-sm font-bold text-gray-900">{{ __('Kelola publikasi') }}</h3>
                        <div class="mt-4 space-y-2">
                            @can('publish', $materi_ajar)
                                @if ($materi_ajar->isDraft())
                                    <form method="POST" action="{{ route('materi.publish', $materi_ajar) }}" onsubmit="return confirm('{{ __('Publikasikan perangkat ajar ini?') }}')">
                                        @csrf
                                        <button type="submit" class="btn-nu-primary w-full justify-center">{{ __('Publikasikan') }}</button>
                                    </form>
                                @endif
                            @endcan

                            @can('archive', $materi_ajar)
                                @unless ($materi_ajar->isDiarsipkan())
                                    <form method="POST" action="{{ route('materi.archive', $materi_ajar) }}" onsubmit="return confirm('{{ __('Arsipkan perangkat ajar ini?') }}')">
                                        @csrf
                                        <button type="submit" class="btn-nu w-full justify-center">{{ __('Arsipkan') }}</button>
                                    </form>
                                @endunless
                            @endcan

                            <a href="{{ route('materi.edit', $materi_ajar) }}" class="btn-nu w-full justify-center">{{ __('Edit data') }}</a>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                        <h3 class="text-sm font-bold text-gray-900">{{ __('Status penggunaan') }}</h3>
                        <form method="POST" action="{{ route('materi.penggunaan', $materi_ajar) }}" class="mt-4 space-y-3">
                            @csrf
                            @method('PATCH')
                            <select name="status_penggunaan" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                @foreach (\App\Models\MateriAjar::STATUS_PENGGUNAAN_OPTIONS as $opt)
                                    <option value="{{ $opt }}" @selected($materi_ajar->status_penggunaan === $opt)>
                                        {{ (new \App\Models\MateriAjar(['status_penggunaan' => $opt]))->labelStatusPenggunaan() }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-nu w-full justify-center">{{ __('Perbarui status') }}</button>
                        </form>
                    </div>

                    @can('delete', $materi_ajar)
                        <form method="POST" action="{{ route('materi.destroy', $materi_ajar) }}" onsubmit="return confirm('{{ __('Hapus perangkat ajar ini?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                                {{ __('Hapus') }}
                            </button>
                        </form>
                    @endcan
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
