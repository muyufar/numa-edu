@php
    $latest = Auth::user()->notifications()->take(5)->get();
@endphp

<div class="px-4 py-3 border-b border-gray-100">
    <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-gray-900">{{ __('Notifikasi') }}</div>
        @include('notifications._mark_all_form')
    </div>
</div>

<div class="max-h-80 overflow-auto">
    @forelse ($latest as $n)
        @include('notifications._item', ['n' => $n, 'compact' => true])
    @empty
        <div class="px-4 py-6 text-center text-sm text-gray-500">{{ __('Belum ada notifikasi.') }}</div>
    @endforelse
</div>

<div class="border-t border-gray-100 px-4 py-3">
    <a href="{{ route('notifications.index') }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Lihat semua') }} →</a>
</div>