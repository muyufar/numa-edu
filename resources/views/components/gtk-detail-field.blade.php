@props(['label', 'value'])

<div class="grid gap-1 border-b border-gray-50 py-3 sm:grid-cols-12 sm:gap-4">
    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 sm:col-span-4">{{ $label }}</dt>
    <dd class="text-sm text-gray-900 sm:col-span-8">{{ $value }}</dd>
</div>
