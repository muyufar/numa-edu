@php
    $map = [
        'submitted' => __('Dikirim'),
        'verified' => __('Diverifikasi'),
        'accepted' => __('Diterima'),
        'rejected' => __('Ditolak'),
    ];
@endphp
<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1
    @if ($status === 'accepted') bg-emerald-50 text-emerald-800 ring-emerald-200
    @elseif ($status === 'rejected') bg-red-50 text-red-800 ring-red-200
    @elseif ($status === 'verified') bg-sky-50 text-sky-900 ring-sky-200
    @else bg-gray-100 text-gray-800 ring-gray-200
    @endif">
    {{ $map[$status] ?? $status }}
</span>
