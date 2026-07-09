@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\MateriAjar[] $items */
    $tabLink = fn (string $key) => route('materi.index', array_filter(array_merge(request()->query(), ['tab' => $key])));
    $badgeClass = fn (string $tone) => match ($tone) {
        'sky' => 'bg-sky-50 text-sky-800 ring-sky-100',
        'emerald' => 'bg-emerald-50 text-emerald-800 ring-emerald-100',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-100',
        'gray' => 'bg-gray-100 text-gray-700 ring-gray-200',
        'violet' => 'bg-violet-50 text-violet-800 ring-violet-100',
        default => 'bg-gray-100 text-gray-700 ring-gray-200',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ __('Perangkat Ajar') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Publikasi dan arsip modul ajar, RPP, modul pembelajaran, LKPD, dan bahan ajar guru secara rapi per mapel & tahun ajaran.') }}</p>
            </div>
            @can('create', \App\Models\MateriAjar::class)
                <a href="{{ route('materi.create') }}" class="btn-nu-primary">{{ __('Unggah perangkat ajar') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            @unless (auth()->user()->hasAnyRole(['siswa', 'wali']))
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <a href="{{ $tabLink('rencana') }}" class="rounded-2xl border border-amber-100 bg-amber-50/70 p-4 shadow-sm ring-1 ring-black/5 hover:ring-amber-200">
                        <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">{{ __('Akan digunakan') }}</div>
                        <div class="mt-1 text-2xl font-extrabold text-amber-900">{{ $stats['rencana'] }}</div>
                    </a>
                    <a href="{{ $tabLink('aktif') }}" class="rounded-2xl border border-sky-100 bg-sky-50/70 p-4 shadow-sm ring-1 ring-black/5 hover:ring-sky-200">
                        <div class="text-xs font-semibold uppercase tracking-wide text-sky-700">{{ __('Sedang digunakan') }}</div>
                        <div class="mt-1 text-2xl font-extrabold text-sky-900">{{ $stats['aktif'] }}</div>
                    </a>
                    <a href="{{ $tabLink('selesai') }}" class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 shadow-sm ring-1 ring-black/5 hover:ring-emerald-200">
                        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ __('Sudah digunakan') }}</div>
                        <div class="mt-1 text-2xl font-extrabold text-emerald-900">{{ $stats['selesai'] }}</div>
                    </a>
                    <a href="{{ $tabLink('draft') }}" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm ring-1 ring-black/5 hover:ring-gray-300">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Draft') }}</div>
                        <div class="mt-1 text-2xl font-extrabold text-gray-900">{{ $stats['draft'] }}</div>
                    </a>
                    <a href="{{ $tabLink('arsip') }}" class="rounded-2xl border border-violet-100 bg-violet-50/70 p-4 shadow-sm ring-1 ring-black/5 hover:ring-violet-200">
                        <div class="text-xs font-semibold uppercase tracking-wide text-violet-700">{{ __('Arsip') }}</div>
                        <div class="mt-1 text-2xl font-extrabold text-violet-900">{{ $stats['arsip'] }}</div>
                    </a>
                </div>
            @endunless

            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">
                <div class="border-b border-gray-100 bg-gray-50 p-4 space-y-4">
                    @unless (auth()->user()->hasAnyRole(['siswa', 'wali']))
                        <div class="flex flex-wrap gap-2">
                            @foreach ([
                                'semua' => __('Semua'),
                                'rencana' => __('Akan digunakan'),
                                'aktif' => __('Sedang digunakan'),
                                'selesai' => __('Sudah digunakan'),
                                'draft' => __('Draft'),
                                'arsip' => __('Arsip'),
                            ] as $key => $label)
                                <a href="{{ $tabLink($key) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 {{ $tab === $key ? 'bg-nu-primary text-white ring-nu-primary' : 'bg-white text-gray-700 ring-gray-200 hover:bg-gray-50' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    @endunless

                    <form method="GET" class="grid gap-3 md:grid-cols-12">
                        <input type="hidden" name="tab" value="{{ $tab }}" />
                        <div class="md:col-span-3">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari judul, mapel, guru...') }}" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                        </div>
                        <div class="md:col-span-2">
                            <select name="jenis" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                <option value="">{{ __('Semua jenis') }}</option>
                                @foreach (\App\Models\MateriAjar::JENIS_OPTIONS as $j)
                                    <option value="{{ $j }}" @selected(request('jenis') === $j)>
                                        {{ (new \App\Models\MateriAjar(['jenis' => $j]))->labelJenis() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <select name="mata_pelajaran_id" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                <option value="">{{ __('Semua mapel') }}</option>
                                @foreach ($mapelOptions as $m)
                                    <option value="{{ $m->id }}" @selected((string) request('mata_pelajaran_id') === (string) $m->id)>
                                        {{ $m->kode ? $m->kode.' - ' : '' }}{{ $m->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <select name="tahun_ajaran" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                <option value="">{{ __('Tahun ajaran') }}</option>
                                @foreach ($tahunAjaranOptions as $t)
                                    <option value="{{ $t }}" @selected(request('tahun_ajaran') === $t)>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        @unless (auth()->user()->hasAnyRole(['siswa', 'wali']))
                            <div class="md:col-span-2">
                                <select name="guru_id" class="w-full rounded-xl border-gray-200 bg-white shadow-sm">
                                    <option value="">{{ __('Semua guru') }}</option>
                                    @foreach ($guruOptions as $g)
                                        <option value="{{ $g->id }}" @selected((string) request('guru_id') === (string) $g->id)>{{ $g->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endunless
                        <div class="md:col-span-1 flex gap-2">
                            <button class="btn-nu-primary w-full" type="submit">{{ __('Filter') }}</button>
                        </div>
                    </form>
                </div>

                @if ($tab === 'arsip' && $arsipGroups->isNotEmpty())
                    <div class="border-b border-gray-100 bg-violet-50/40 px-5 py-4">
                        <h3 class="text-sm font-bold text-violet-900">{{ __('Arsip per tahun ajaran & mapel') }}</h3>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($arsipGroups as $groupLabel => $groupItems)
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-violet-800 ring-1 ring-violet-100">
                                    {{ $groupLabel }} <span class="ms-1 text-violet-500">({{ $groupItems->count() }})</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-white">
                            <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-3">{{ __('Judul') }}</th>
                                <th class="px-5 py-3">{{ __('Jenis') }}</th>
                                <th class="px-5 py-3">{{ __('Mapel / Kelas') }}</th>
                                <th class="px-5 py-3">{{ __('Status') }}</th>
                                <th class="px-5 py-3">{{ __('Periode') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse ($items as $it)
                                <tr class="text-sm text-gray-700">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('materi.show', $it) }}" class="font-semibold text-gray-900 hover:text-nu-primary hover:underline">{{ $it->judul }}</a>
                                        <div class="mt-0.5 text-xs text-gray-500">
                                            {{ $it->guru?->nama ?: __('Guru tidak diisi') }}
                                            @if ($it->pertemuan_ke) · {{ __('Pertemuan') }} {{ $it->pertemuan_ke }} @endif
                                        </div>
                                        @if ($it->deskripsi)
                                            <div class="mt-2 line-clamp-2 max-w-xl text-xs text-gray-500">{{ $it->deskripsi }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $badgeClass('gray') }}">
                                            {{ $it->labelJenis() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">{{ $it->mataPelajaran->nama ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $it->kelas ? ($it->kelas->tingkat.' '.$it->kelas->nama) : __('Semua kelas') }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $badgeClass(match($it->status_publikasi) { 'dipublikasi' => 'emerald', 'diarsipkan' => 'violet', default => 'gray' }) }}">
                                                {{ $it->labelStatusPublikasi() }}
                                            </span>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $badgeClass(match($it->status_penggunaan) { 'aktif' => 'sky', 'rencana' => 'amber', default => 'emerald' }) }}">
                                                {{ $it->labelStatusPenggunaan() }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">
                                        <div>{{ $it->tahun_ajaran ?: '—' }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $it->semester ? __('Semester').' '.$it->semester : '—' }}
                                            @if ($it->tanggal) · {{ $it->tanggal->format('d/m/Y') }} @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                        @can('view', $it)
                                            @if ($it->isPdf())
                                                <a href="{{ route('materi.show', $it) }}#baca-pdf" class="text-sm font-bold text-sky-700 hover:underline">{{ __('Baca') }}</a>
                                            @endif
                                            <a href="{{ route('materi.download', $it) }}" class="{{ $it->isPdf() ? 'ms-3 ' : '' }}text-sm font-bold text-nu-primary hover:underline">{{ __('Unduh') }}</a>
                                        @endcan
                                        @can('update', $it)
                                            <a href="{{ route('materi.edit', $it) }}" class="ms-3 text-sm font-bold text-gray-700 hover:underline">{{ __('Edit') }}</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">
                                        {{ __('Belum ada perangkat ajar untuk filter ini.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 bg-white p-4">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
