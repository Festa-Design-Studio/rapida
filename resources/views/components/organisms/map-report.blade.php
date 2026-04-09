@props([
    'latitude',
    'longitude',
    'damageLevel' => 'partial',
    'label' => '',
    'height' => 'h-48',
    'zoom' => 17,
])

@php
    $hasCoordinates = $latitude && $longitude;
@endphp

<div
    {{ $attributes->class(['relative rounded-xl overflow-hidden border border-slate-200', $height]) }}
    role="region"
    aria-label="Report location map"
>
    @if($hasCoordinates)
        <div
            x-data="typeof rapidaReportMap !== 'undefined' ? rapidaReportMap({
                center: [{{ $longitude }}, {{ $latitude }}],
                zoom: {{ $zoom }},
                damageLevel: '{{ $damageLevel }}',
                label: '{{ addslashes($label) }}',
                tokens: {
                    damage_minimal: '#22c55e',   // damage-minimal-map
                    damage_partial: '#f59e0b',   // damage-partial-map
                    damage_complete: '#c46b5a',  // crisis-rose-400
                    footprint_fill: '#2e6689',   // rapida-blue-700
                    footprint_stroke: '#1a3a4a', // rapida-blue-900
                    user_dot: '#2e6689',         // rapida-blue-700
                },
            }) : {}"
            x-init="if (typeof rapidaReportMap !== 'undefined' && init) init()"
            class="absolute inset-0"
            aria-label="Map showing report location"
        ></div>

        {{-- Legend --}}
        <div class="absolute bottom-3 left-3 z-10 bg-white/90 backdrop-blur-sm rounded-lg p-2.5 shadow-sm">
            <div class="space-y-1">
                @foreach([
                    ['minimal', 'bg-green-500', 'Minimal'],
                    ['partial', 'bg-amber-500', 'Partial'],
                    ['complete', 'bg-red-600', 'Complete'],
                ] as [$level, $color, $labelText])
                    <div class="flex items-center gap-1.5 {{ $damageLevel === $level ? 'font-medium' : 'opacity-50' }}">
                        <span class="h-2.5 w-2.5 rounded-full {{ $color }} {{ $damageLevel === $level ? 'ring-2 ring-offset-1 ring-' . ($level === 'minimal' ? 'green-400' : ($level === 'partial' ? 'amber-400' : 'red-400')) : '' }}" aria-hidden="true"></span>
                        <span class="text-caption text-slate-600">{{ $labelText }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="absolute inset-0 bg-slate-100 flex items-center justify-center">
            <div class="text-center space-y-2">
                <x-atoms.icon name="pin" size="lg" class="text-slate-400 mx-auto" />
                <p class="text-body-sm text-slate-500">Location not available</p>
            </div>
        </div>
    @endif
</div>
