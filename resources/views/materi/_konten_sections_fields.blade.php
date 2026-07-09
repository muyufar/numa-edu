@php
    use App\Support\PerangkatAjarJenis;

    $groups = PerangkatAjarJenis::groupLabels($jenis);
    $fieldsByGroup = collect(PerangkatAjarJenis::kontenFields($jenis))->groupBy('group');
@endphp

@foreach ($groups as $groupKey => $groupLabel)
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __($groupLabel) }}</h3>
        <div class="mt-4 space-y-4">
            @foreach ($fieldsByGroup->get($groupKey, []) as $fieldKey => $meta)
                <div>
                    <label class="text-sm font-semibold text-gray-700">{{ __($meta['label']) }}</label>
                    <textarea
                        name="konten_modul[{{ $fieldKey }}]"
                        rows="{{ $meta['rows'] }}"
                        class="{{ $fieldClass }}"
                        placeholder="{{ $meta['placeholder'] ?? '' }}"
                    >{{ $konten[$fieldKey] ?? '' }}</textarea>
                    @error("konten_modul.$fieldKey")<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
                </div>
            @endforeach
        </div>
    </div>
@endforeach
