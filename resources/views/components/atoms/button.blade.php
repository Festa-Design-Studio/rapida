@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'loading' => false,
    'disabled' => false,
])

@php
    $variantClasses = match($variant) {
        'primary'   => 'bg-rapida-blue-900 text-white hover:bg-rapida-blue-900 focus:ring-rapida-blue-700',
        'secondary' => 'bg-white text-rapida-blue-900 border-2 border-rapida-blue-900 hover:bg-rapida-blue-50 focus:ring-rapida-blue-700',
        'ghost'     => 'bg-transparent text-rapida-blue-900 hover:bg-rapida-blue-50 focus:ring-rapida-blue-700',
        'danger'    => 'bg-red-700 text-white hover:bg-red-800 focus:ring-red-600',
        'safe-exit' => 'bg-slate-600 text-white hover:bg-slate-700 focus:ring-slate-500',
        'icon-only' => 'bg-transparent text-rapida-blue-900 hover:bg-rapida-blue-50 focus:ring-rapida-blue-700',
        default     => 'bg-rapida-blue-900 text-white hover:bg-rapida-blue-900 focus:ring-rapida-blue-700',
    };

    $sizeClasses = match($size) {
        'lg'   => 'h-[56px] px-8 py-4 text-btn',
        'md'   => 'h-12 px-6 py-3 text-btn',
        'sm'   => 'h-10 px-4 py-2 text-btn-sm',
        'icon' => 'h-12 w-12 p-3',
        default => 'h-12 px-6 py-3 text-btn',
    };

    $baseClasses = 'inline-flex items-center justify-center gap-2 font-heading font-semibold rounded-lg
        cursor-pointer active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-offset-2
        transition-colors duration-150
        disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none';

    $classes = "$baseClasses $variantClasses $sizeClasses";
@endphp

@if($href)
    <a
        href="{{ $href }}"
        {{ $attributes->class([$classes]) }}
        @if($disabled) aria-disabled="true" tabindex="-1" @endif
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->class([$classes]) }}
        @if($disabled || $loading) disabled @endif
        @if($loading) aria-live="polite" @endif
    >
        @if($loading)
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
