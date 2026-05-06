@php
    $map = [
        'hadir' => __('Hadir'),
        'izin' => __('Izin'),
        'sakit' => __('Sakit'),
        'alpa' => __('Alpa'),
    ];
@endphp
{{ $map[$status] ?? $status }}
