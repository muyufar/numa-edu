@php
    $labels = [
        'manual' => __('Manual'),
        'barcode' => __('Barcode/QR'),
        'face' => __('Wajah'),
    ];
@endphp
{{ $labels[$metode] ?? $metode }}
