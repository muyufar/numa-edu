@php
    $unreadCount = Auth::user()->unreadNotifications()->count();
@endphp

@if ($unreadCount > 0)
    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf
        <button type="submit" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
            {{ __('Tandai semua dibaca') }}
        </button>
    </form>
@endif