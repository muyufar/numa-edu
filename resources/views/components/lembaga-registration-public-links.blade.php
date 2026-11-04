@props(['registration'])

@php
    /** @var \App\Models\LembagaRegistration $registration */
    $links = $registration->publicPortalLinks();
    $shareText = $registration->publicPortalShareText();
@endphp

<div
    class="rounded-2xl border border-sky-200 bg-sky-50/50 p-5 shadow-sm ring-1 ring-sky-200/60"
    x-data="{
        copiedKey: null,
        copy(text, key) {
            navigator.clipboard.writeText(text).then(() => {
                this.copiedKey = key;
                setTimeout(() => { if (this.copiedKey === key) this.copiedKey = null; }, 2000);
            });
        }
    }"
>
    <h3 class="text-sm font-bold uppercase tracking-wide text-sky-900">{{ __('Tautan untuk lembaga') }}</h3>
    <p class="mt-1 text-xs leading-relaxed text-sky-950/80">
        {{ __('Bagikan tautan pribadi ini ke operator lembaga (WhatsApp/email) jika mereka kehilangan link setelah mengirim formulir.') }}
    </p>

    <div class="mt-4 space-y-3">
        @foreach ($links as $link)
            <div class="rounded-xl border border-sky-200/80 bg-white p-3">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-gray-800">{{ $link['label'] }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $link['hint'] }}</p>
                    </div>
                    <a
                        href="{{ $link['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="shrink-0 text-xs font-semibold text-nu-primary hover:underline"
                    >{{ __('Buka') }}</a>
                </div>
                <div class="mt-2 flex gap-2">
                    <input
                        type="text"
                        readonly
                        value="{{ $link['url'] }}"
                        class="block min-w-0 flex-1 rounded-lg border-gray-200 bg-gray-50 font-mono text-[11px] text-gray-800 shadow-sm"
                        aria-label="{{ $link['label'] }}"
                    />
                    <button
                        type="button"
                        class="shrink-0 rounded-lg border border-sky-300 bg-white px-3 py-1.5 text-xs font-semibold text-sky-900 hover:bg-sky-50"
                        x-on:click="copy(@js($link['url']), @js($link['key']))"
                    >
                        <span x-show="copiedKey !== @js($link['key'])">{{ __('Salin') }}</span>
                        <span x-show="copiedKey === @js($link['key'])" x-cloak>{{ __('Tersalin!') }}</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        <button
            type="button"
            class="inline-flex items-center rounded-xl border border-sky-400 bg-white px-4 py-2 text-sm font-semibold text-sky-950 hover:bg-sky-100"
            x-on:click="copy(@js($shareText), 'share-all')"
        >
            <span x-show="copiedKey !== 'share-all'">{{ __('Salin teks untuk dibagikan') }}</span>
            <span x-show="copiedKey === 'share-all'" x-cloak>{{ __('Tersalin!') }}</span>
        </button>
    </div>
</div>
