@props([
    'label',
    'value',
    'hint' => null,
])

<div {{ $attributes->merge(['class' => 'nu-surface p-5 ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-md']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 tabular-nums">{{ $value }}</p>
            @if($hint)
                <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
            @endif
        </div>
        @isset($icon)
            <div class="shrink-0 rounded-xl bg-nu-primary/10 p-2 text-nu-primary [&_svg]:h-6 [&_svg]:w-6">
                {{ $icon }}
            </div>
        @endisset
    </div>
</div>
