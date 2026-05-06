@php
    $map = [
        'unpaid' => __('Belum lunas'),
        'partial' => __('Sebagian'),
        'paid' => __('Lunas'),
    ];
@endphp
<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1
    @if ($status === 'paid') bg-emerald-50 text-emerald-800 ring-emerald-200
    @elseif ($status === 'partial') bg-amber-50 text-amber-900 ring-amber-200
    @else bg-gray-100 text-gray-700 ring-gray-200
    @endif">
    {{ $map[$status] ?? $status }}
</span>
