@props([
    'label',
    'groupActive' => false,
    'expanded' => 'false',
    'badge' => null,
    'linkBase',
    'activeClass',
    'idleClass',
])

<div
    x-data="{ open: {{ $expanded }}, fly: false }"
    class="relative space-y-1"
    @mouseenter="if (sidebarCollapsed) fly = true"
    @mouseleave="fly = false"
>
    <button
        type="button"
        @click="open = !open"
        class="{{ $linkBase }} w-full {{ $groupActive ? $activeClass : $idleClass }}"
    >
        @isset($icon)
            <span class="inline-flex shrink-0">{{ $icon }}</span>
        @endisset
        <span class="flex-1 text-left" x-show="!sidebarCollapsed" x-cloak>{{ $label }}</span>
        @if ($badge)
            <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-nu-gold" x-show="!sidebarCollapsed" x-cloak>{{ $badge }}</span>
        @endif
        <svg class="nu-chevron h-4 w-4 shrink-0 text-white/70 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="fly && sidebarCollapsed"
        x-transition.opacity.scale.origin.left
        x-cloak
        class="absolute left-full top-0 z-50 ml-3 w-56 max-h-[min(70vh,28rem)] overflow-y-auto rounded-2xl border border-gray-200 bg-white text-gray-900 shadow-xl ring-1 ring-black/5"
    >
        <div class="sticky top-0 z-10 border-b border-gray-100 bg-gray-50 px-4 py-3">
            <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $label }}</div>
        </div>
        <div class="space-y-1 p-2 pb-3">
            {{ $flyout ?? $slot }}
        </div>
    </div>

    <div x-show="open && !sidebarCollapsed" x-collapse x-cloak class="space-y-1">
        {{ $slot }}
    </div>
</div>
