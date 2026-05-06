@props(['value', 'decimals' => 2])
@if ($value === null || $value === '')
    <span class="text-gray-400">—</span>
@else
    Rp&nbsp;{{ number_format((float) $value, (int) $decimals, ',', '.') }}
@endif
