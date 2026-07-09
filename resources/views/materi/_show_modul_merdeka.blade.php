@php
    use App\Support\ModulAjarMerdeka;

    /** @var \App\Models\MateriAjar $materi_ajar */
    $konten = $materi_ajar->kontenModulNormalized();
    $groups = ModulAjarMerdeka::groupLabels();
    $fieldsByGroup = collect(ModulAjarMerdeka::kontenFields())->groupBy('group');
    $hasKonten = collect($konten)->contains(fn ($v) => trim($v) !== '');
@endphp

@if ($materi_ajar->isModulMerdeka())
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <div class="border-b border-gray-100 pb-4">
            <p class="text-xs font-bold uppercase tracking-wide text-nu-primary">{{ __('Modul Ajar Kurikulum Merdeka') }}</p>
            <h3 class="mt-2 text-lg font-extrabold text-gray-900">{{ $materi_ajar->judul }}</h3>
            @if ($materi_ajar->elemen_topik)
                <p class="mt-1 text-sm text-gray-600">{{ $materi_ajar->elemen_topik }}</p>
            @endif
        </div>

        <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 text-sm">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Guru / penulis') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->guru?->nama ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sekolah') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->sekolah?->nama ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Fase') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->fase ? __('Fase').' '.$materi_ajar->fase : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kelas') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">
                    {{ $materi_ajar->kelas ? $materi_ajar->kelas->tingkat.' '.$materi_ajar->kelas->nama : '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Mata pelajaran') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->mataPelajaran?->nama ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Alokasi waktu') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->alokasi_waktu ?: '—' }}</dd>
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Model pembelajaran') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->model_pembelajaran ?: '—' }}</dd>
            </div>
        </dl>
    </div>

    @if ($hasKonten)
        @foreach ($groups as $groupKey => $groupLabel)
            @php
                $sections = $fieldsByGroup->get($groupKey, collect())->filter(fn ($meta, $key) => trim($konten[$key] ?? '') !== '');
            @endphp
            @if ($sections->isNotEmpty())
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <h3 class="text-sm font-bold text-gray-900">{{ __($groupLabel) }}</h3>
                    <div class="mt-4 space-y-5">
                        @foreach ($sections as $fieldKey => $meta)
                            <div>
                                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __($meta['label']) }}</h4>
                                <div class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-800">{{ $konten[$fieldKey] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @endif
@endif
