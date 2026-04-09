@props([
    'current' => 1,
    'total' => 5,
    'variant' => 'dots',
    'labels' => [],
])

@php
    $percentage = round(($current / $total) * 100);
@endphp

@if($variant === 'dots')
    <div
        class="flex items-center justify-center gap-2 py-3"
        role="progressbar"
        aria-valuenow="{{ $current }}"
        aria-valuemin="1"
        aria-valuemax="{{ $total }}"
        aria-label="Step {{ $current }} of {{ $total }}"
    >
        @for($i = 1; $i <= $total; $i++)
            @if($i < $current)
                <span class="h-2.5 w-2.5 rounded-full bg-rapida-blue-700" aria-label="Step {{ $i }}: Complete"></span>
            @elseif($i === $current)
                <span class="h-3 w-3 rounded-full bg-rapida-blue-700 ring-4 ring-rapida-blue-100" aria-label="Step {{ $i }}: Current"></span>
            @else
                <span class="h-2.5 w-2.5 rounded-full bg-slate-300" aria-label="Step {{ $i }}: Remaining"></span>
            @endif
        @endfor
    </div>

@elseif($variant === 'counter')
    <p class="text-h4 font-medium text-slate-700 text-center" aria-live="polite">
        Step {{ $current }} of {{ $total }}
        @if(isset($labels[$current - 1]))
            — <span class="text-rapida-blue-700">{{ $labels[$current - 1] }}</span>
        @endif
    </p>

@elseif($variant === 'bar')
    <div
        class="w-full bg-slate-200 rounded-full h-1.5"
        role="progressbar"
        aria-valuenow="{{ $percentage }}"
        aria-valuemin="0"
        aria-valuemax="100"
        aria-label="{{ $percentage }}% complete"
    >
        <div
            class="bg-rapida-blue-700 h-1.5 rounded-full transition-all duration-300 ease-out"
            style="width: {{ $percentage }}%"
        ></div>
    </div>
@endif
