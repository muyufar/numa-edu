@props([
    'title' => null,
    'subtitle' => null,
    'badge' => null,
])

<section {{ $attributes->merge(['class' => 'nu-surface p-5 ring-1 ring-black/5']) }}>
    @if ($title || $subtitle || $badge || isset($action))
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-lg font-bold tracking-tight text-gray-900">{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="mt-1 text-xs text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if ($badge)
                    <span class="inline-flex items-center rounded-full bg-gray-900/5 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-gray-700 ring-1 ring-gray-200">
                        {{ $badge }}
                    </span>
                @endif
                @isset($action)
                    {{ $action }}
                @endisset
            </div>
        </div>
    @endif

    <div class="{{ ($title || $subtitle || $badge || isset($action)) ? 'mt-4' : '' }}">
        {{ $slot }}
    </div>
</section>

