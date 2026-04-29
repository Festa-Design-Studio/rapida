@props([
    'undpLogo' => '/images/partners/undp-logo.svg',
    'festaLogomark' => '/images/partners/festa-logomark.svg',
    'festaUrl' => 'https://festa.design/',
])

<footer
    {{ $attributes->class(['bg-white border-t border-slate-200 px-4 sm:px-6 py-3 sm:py-4']) }}
    role="contentinfo"
>
    <div class="max-w-7xl mx-auto flex flex-row items-center justify-between gap-3">
        {{-- Left: UNDP attribution --}}
        <div class="inline-flex items-center gap-2 min-w-0">
            <img
                src="{{ $undpLogo }}"
                alt="UNDP"
                class="h-4 sm:h-5 w-auto shrink-0"
            />
            <span class="text-caption text-slate-600 truncate">
                {{ __('rapida.undp_crisis_mapping_tool') }}
            </span>
        </div>

        {{-- Right: Festa founding partner --}}
        <a
            href="{{ $festaUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 transition-colors shrink-0 min-h-[44px]"
            aria-label="{{ __('rapida.interface_designed_by_festa') }} (opens in a new tab)"
        >
            <img
                src="{{ $festaLogomark }}"
                alt=""
                aria-hidden="true"
                class="h-4 w-4 shrink-0"
            />
            <span class="text-caption hidden sm:inline">
                {{ __('rapida.interface_designed_by_festa') }}
            </span>
        </a>
    </div>
</footer>
