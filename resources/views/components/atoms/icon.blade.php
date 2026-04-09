@props([
    'name',
    'size' => 'md',
])

@php
    $sizes = [
        'xs' => 'w-3 h-3',      // 12px
        'sm' => 'w-4 h-4',      // 16px
        'md' => 'w-5 h-5',      // 20px
        'lg' => 'w-6 h-6',      // 24px
        'xl' => 'w-10 h-10',    // 40px
        '2xl' => 'w-16 h-16',   // 64px
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $svgPath = resource_path("svg/icons/{$name}.svg");
@endphp

<span {{ $attributes->class([$sizeClass, 'inline-flex items-center justify-center']) }} aria-hidden="true">
    @if(file_exists($svgPath))
        {!! file_get_contents($svgPath) !!}
    @else
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
    @endif
</span>
