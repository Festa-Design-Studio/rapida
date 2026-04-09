@props([
    'variant' => 'spinner',
    'size' => 'md',
    'message' => null,
    'percentage' => null,
])

@php
    $spinnerSizes = [
        'sm' => 'h-4 w-4',
        'md' => 'h-8 w-8',
        'lg' => 'h-12 w-12',
    ];
    $spinnerClass = $spinnerSizes[$size] ?? $spinnerSizes['md'];

    $strokeWidths = [
        'sm' => '3',
        'md' => '2.5',
        'lg' => '2',
    ];
    $strokeWidth = $strokeWidths[$size] ?? '2.5';
@endphp

@if($variant === 'spinner')
    @if($size === 'lg' && $message)
        {{-- Full screen overlay --}}
        <div
            class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-white/90 backdrop-blur-sm"
            role="status"
            aria-label="{{ $message }}"
        >
            <svg class="{{ $spinnerClass }} animate-spin text-rapida-blue-700 motion-reduce:animate-pulse"
                 fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-20" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="{{ $strokeWidth }}"/>
                <path class="opacity-80" fill="currentColor"
                      d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <p class="mt-4 text-body text-slate-600 font-medium">{{ $message }}</p>
        </div>
    @else
        {{-- Inline spinner --}}
        <svg class="{{ $spinnerClass }} animate-spin text-current motion-reduce:animate-pulse"
             fill="none" viewBox="0 0 24 24"
             role="status"
             aria-label="{{ $message ?? 'Loading' }}"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="{{ $strokeWidth }}"/>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8v8z"/>
        </svg>
    @endif

@elseif($variant === 'skeleton-text')
    <div class="space-y-2 animate-pulse motion-reduce:animate-none" aria-busy="true" aria-label="Loading content">
        <div class="h-4 bg-slate-200 rounded w-3/4"></div>
        <div class="h-4 bg-slate-200 rounded w-1/2"></div>
    </div>

@elseif($variant === 'skeleton-card')
    <div class="rounded-xl border border-slate-200 p-4 space-y-3 animate-pulse motion-reduce:animate-none"
         aria-busy="true" aria-label="Loading report">
        <div class="h-32 w-full bg-slate-200 rounded-lg"></div>
        <div class="h-5 w-28 bg-slate-200 rounded-full"></div>
        <div class="space-y-2">
            <div class="h-4 bg-slate-200 rounded w-3/4"></div>
            <div class="h-4 bg-slate-200 rounded w-1/2"></div>
        </div>
        <div class="flex gap-2">
            <div class="h-3 w-16 bg-slate-200 rounded"></div>
            <div class="h-3 w-20 bg-slate-200 rounded"></div>
        </div>
    </div>

@elseif($variant === 'skeleton-image')
    <div class="bg-slate-200 rounded-lg animate-pulse motion-reduce:animate-none aspect-video"
         aria-busy="true" aria-label="Loading image"></div>

@elseif($variant === 'progress-ring' && $percentage !== null)
    @php
        $circumference = 2 * M_PI * 28;
        $offset = $circumference - ($percentage / 100) * $circumference;
    @endphp
    <div class="relative flex items-center justify-center h-16 w-16">
        <svg class="h-16 w-16 -rotate-90" viewBox="0 0 64 64" aria-hidden="true">
            <circle cx="32" cy="32" r="28"
                    fill="none" stroke="#e2e8f0" stroke-width="4"/>
            <circle cx="32" cy="32" r="28"
                    fill="none" stroke="#2e6689" stroke-width="4"
                    stroke-linecap="round"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $offset }}"
                    class="transition-all duration-300"/>
        </svg>
        <span class="absolute text-caption font-semibold text-rapida-blue-700"
              aria-live="polite">{{ $percentage }}%</span>
    </div>
@endif
