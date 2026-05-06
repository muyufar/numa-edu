@php
    /** @var \Illuminate\Notifications\DatabaseNotification $n */
    $data = is_array($n->data) ? $n->data : (array) $n->data;
    $title = $data['title'] ?? class_basename($n->type);
    $body = $data['body'] ?? '';
    $url = $data['url'] ?? null;
    $isUnread = $n->read_at === null;
    $compact = $compact ?? false;
@endphp

<div class="px-4 py-3 {{ $compact ? '' : 'border-b border-gray-100' }} {{ $isUnread ? 'bg-amber-50/40' : '' }}">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="text-sm font-semibold text-gray-900">{{ $title }}</div>
            @if ($body)
                <div class="mt-0.5 text-xs text-gray-600">{{ $body }}</div>
            @endif
            <div class="mt-1 text-[11px] text-gray-400">{{ $n->created_at?->diffForHumans() }}</div>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            @if ($url)
                <a href="{{ $url }}" class="text-xs font-semibold text-nu-primary hover:underline">{{ __('Buka') }}</a>
            @endif
            @if ($isUnread)
                <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-gray-600 hover:text-gray-900">{{ __('Baca') }}</button>
                </form>
            @endif
        </div>
    </div>
</div>