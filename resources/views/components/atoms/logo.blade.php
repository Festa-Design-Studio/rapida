@props([
    'size' => 'md',
    'variant' => 'mark',
    'showText' => true,
])

@php
    // Responsive logo sizes — clamp from large to small screens
    $sizes = [
        'xs' => 'w-5 h-5',       // 20px — favicon context, inline badges
        'sm' => 'w-6 h-6',       // 24px — compact header, mobile nav
        'md' => 'w-8 h-8',       // 32px — default header
        'lg' => 'w-10 h-10',     // 40px — dashboard headers
        'xl' => 'w-14 h-14',     // 56px — onboarding, hero
        '2xl' => 'w-20 h-20',    // 80px — splash, about page
    ];

    $textSizes = [
        'xs' => 'text-caption',
        'sm' => 'text-body-sm',
        'md' => 'text-h4',
        'lg' => 'text-h3',
        'xl' => 'text-h2',
        '2xl' => 'text-h1',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $textClass = $textSizes[$size] ?? $textSizes['md'];

    // Variants: mark (icon only), full (icon + text), responsive (icon always, text hidden on mobile)
    $showTextFinal = match($variant) {
        'mark' => false,
        'full' => true,
        'responsive' => true,
        default => $showText,
    };

    $textVisibility = $variant === 'responsive' ? 'hidden sm:inline' : '';
@endphp

<div {{ $attributes->class(['inline-flex items-center gap-2']) }}>
    {{-- Logomark — inline SVG for zero HTTP requests (low-bandwidth safe) --}}
    <span class="{{ $sizeClass }} inline-flex items-center justify-center shrink-0" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="w-full h-full" fill="currentColor">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M30.6273 3.21448L29.3503 19.0905H45.2975L21.3727 44.7856L22.6497 28.9095H6.70253L30.6273 3.21448Z"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M11 8H18V11H11V8Z" opacity="0.7"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M5 8H8.01V11H5V8Z" opacity="0.7"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M7 37H18V40H7V37Z" opacity="0.7"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M7 18L1 18L0.999992 15L6.99999 15L7 18Z" opacity="0.7"/>
        </svg>
    </span>

    {{-- Wordmark --}}
    @if($showTextFinal)
        <span class="{{ $textClass }} font-heading font-bold tracking-tight {{ $textVisibility }}">
            {{ __('rapida.app_name') }}
        </span>
    @endif
</div>
