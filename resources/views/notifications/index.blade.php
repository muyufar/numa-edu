<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Notifikasi') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Daftar notifikasi akun ini.') }}</p>
            </div>
            <div class="text-xs font-semibold text-gray-500">
                {{ __('Belum dibaca') }}: {{ $unreadCount }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar') }}</div>
                <div>
                    @include('notifications._mark_all_form')
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($notifications as $n)
                    @include('notifications._item', ['n' => $n, 'compact' => false])
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada notifikasi.') }}</div>
                @endforelse
            </div>
            @if ($notifications->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>