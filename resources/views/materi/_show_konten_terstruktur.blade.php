@php
    use App\Support\LkpdSistematika;
    use App\Support\PerangkatAjarJenis;

    /** @var \App\Models\MateriAjar $materi_ajar */
    $jenis = $materi_ajar->jenis;
    $konten = $materi_ajar->kontenModulNormalized();
    $lkpdAlternatif = $materi_ajar->isLkpd() ? $materi_ajar->lkpdSistematika() : null;
    $groups = PerangkatAjarJenis::groupLabels($jenis);
    $fieldsByGroup = $materi_ajar->isLkpd()
        ? collect(LkpdSistematika::kontenFields($lkpdAlternatif))->groupBy('group')
        : collect(PerangkatAjarJenis::kontenFields($jenis))->groupBy('group');
    $hasKonten = collect($konten)->filter(fn ($v, $k) => ! str_starts_with((string) $k, '_'))->contains(fn ($v) => trim($v) !== '');
    $isGuru = auth()->user()?->hasAnyRole(['guru', 'admin', 'pengurus_cabang', 'superadmin']);
    $headerTone = match ($jenis) {
        'rpp' => ['label' => __('RPP — Rencana Pelaksanaan Pembelajaran'), 'class' => 'text-amber-800'],
        'modul_pembelajaran' => ['label' => __('Modul Pembelajaran'), 'class' => 'text-sky-800'],
        'lkpd' => ['label' => __('LKPD — Lembar Kerja Peserta Didik'), 'class' => 'text-violet-800'],
        default => ['label' => '', 'class' => ''],
    };
@endphp

@if ($materi_ajar->hasKontenTerstruktur() && $jenis !== 'modul')
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <div class="border-b border-gray-100 pb-4">
            <p class="text-xs font-bold uppercase tracking-wide {{ $headerTone['class'] }}">{{ $headerTone['label'] }}</p>
            <h3 class="mt-2 text-lg font-extrabold text-gray-900">{{ $materi_ajar->judul }}</h3>
            @if ($materi_ajar->elemen_topik)
                <p class="mt-1 text-sm text-gray-600">
                    @if ($materi_ajar->isLkpd())
                        <span class="font-medium text-gray-500">{{ __('Materi ajar:') }}</span>
                    @endif
                    {{ $materi_ajar->elemen_topik }}
                </p>
            @endif
            @if ($materi_ajar->isLkpd())
                <p class="mt-2 text-xs text-gray-500">{{ __('Sistematika:') }} {{ LkpdSistematika::labelAlternatif($lkpdAlternatif) }}</p>
            @else
                <p class="mt-2 text-xs text-gray-500">{{ __('Fokus:') }} {{ PerangkatAjarJenis::fokusJenis($jenis) }}</p>
            @endif
        </div>

        <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 text-sm">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Guru / penulis') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->guru?->nama ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Satuan pendidikan') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->sekolah?->nama ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Mata pelajaran') }}</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->mataPelajaran?->nama ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Kelas') }}@if ($materi_ajar->semester) / {{ __('Semester') }}@endif</dt>
                <dd class="mt-1 font-semibold text-gray-900">
                    {{ $materi_ajar->kelas ? $materi_ajar->kelas->tingkat.' '.$materi_ajar->kelas->nama : '—' }}
                    @if ($materi_ajar->semester)
                        <span class="text-gray-500"> · {{ $materi_ajar->semester }}</span>
                    @endif
                </dd>
            </div>
            @if ($materi_ajar->fase)
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Fase') }}</dt>
                    <dd class="mt-1 font-semibold text-gray-900">{{ __('Fase') }} {{ $materi_ajar->fase }}</dd>
                </div>
            @endif
            @if ($materi_ajar->alokasi_waktu && ! $materi_ajar->isLkpd())
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Alokasi waktu') }}</dt>
                    <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->alokasi_waktu }}</dd>
                </div>
            @endif
            @if ($materi_ajar->isLkpd())
                @if ($materi_ajar->alokasi_waktu)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Alokasi waktu') }}</dt>
                        <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->alokasi_waktu }}</dd>
                    </div>
                @endif
                @if ($materi_ajar->pertemuan_ke)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Pertemuan ke-') }}</dt>
                        <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->pertemuan_ke }}</dd>
                    </div>
                @endif
            @endif
            @if ($materi_ajar->isRpp())
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Pertemuan ke-') }}</dt>
                    <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->pertemuan_ke ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Model pembelajaran') }}</dt>
                    <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->model_pembelajaran ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tanggal') }}</dt>
                    <dd class="mt-1 font-semibold text-gray-900">{{ $materi_ajar->tanggal?->format('d M Y') ?: '—' }}</dd>
                </div>
            @endif
        </dl>
    </div>

    @if ($hasKonten)
        @foreach ($groups as $groupKey => $groupLabel)
            @php
                $sections = $fieldsByGroup->get($groupKey, collect())->filter(function ($meta, $key) use ($konten, $isGuru) {
                    if (trim($konten[$key] ?? '') === '') {
                        return false;
                    }
                    if (! empty($meta['guru_only']) && ! $isGuru) {
                        return false;
                    }

                    return true;
                });
            @endphp
            @if ($sections->isNotEmpty())
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <h3 class="text-sm font-bold text-gray-900">{{ __($groupLabel) }}</h3>
                    <div class="mt-4 space-y-5">
                        @foreach ($sections as $fieldKey => $meta)
                            <div>
                                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    {{ __($meta['label']) }}
                                    @if (! empty($meta['guru_only']))
                                        <span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-800">{{ __('Guru') }}</span>
                                    @endif
                                </h4>
                                <div class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-800">{{ $konten[$fieldKey] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @endif
@endif
