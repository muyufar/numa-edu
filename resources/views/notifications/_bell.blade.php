@php
    $unreadCount = Auth::user()->unreadNotifications()->count();
    $hasUnread = $unreadCount > 0;
@endphp

@if ($hasUnread)
    <style>
        @keyframes nu-bell-ring {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(-12deg); }
            20% { transform: rotate(10deg); }
            30% { transform: rotate(-10deg); }
            40% { transform: rotate(8deg); }
            50% { transform: rotate(-6deg); }
            60% { transform: rotate(4deg); }
            70% { transform: rotate(-2deg); }
            80% { transform: rotate(1deg); }
            100% { transform: rotate(0deg); }
        }
        @keyframes nu-dot-blink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(0.92); }
        }
    </style>
@endif

<button
    type="button"
    class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border shadow-sm transition
        {{ $hasUnread ? 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}"
    aria-label="{{ __('Notifikasi') }}"
>
    <svg
        class="h-5 w-5"
        style="{{ $hasUnread ? 'transform-origin: 50% 10%; animation: nu-bell-ring 1.2s ease-in-out infinite;' : '' }}"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
    >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z" />
    </svg>
    @if ($hasUnread)
        <span
            class="pointer-events-none absolute -right-1 -top-1 inline-flex h-2.5 w-2.5 rounded-full bg-red-600 ring-4 ring-white"
            style="animation: nu-dot-blink 1.1s ease-in-out infinite;"
        ></span>
    @endif
</button>