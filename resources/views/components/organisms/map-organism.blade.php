@props([
    'height' => 'h-[500px]',
    'reports' => [],
    'crisisSlug' => null,
    'mode' => 'reporter',
    'centerLat' => 5.56,
    'centerLng' => -0.20,
    'zoom' => 13,
    'fullscreen' => false,
])

@php
    try {
        $slug = $crisisSlug ?? \App\Models\Crisis::where('status', 'active')->value('slug');
    } catch (\Exception $e) {
        $slug = null;
    }
    $hasSlug = !empty($slug);
    $containerClass = $fullscreen
        ? 'relative overflow-hidden'
        : 'relative rounded-xl overflow-hidden border border-slate-200';
@endphp

<div
    {{ $attributes->class([$containerClass, $height]) }}
    role="region"
    aria-label="Damage report map"
>
    @if($hasSlug)
        {{-- Real MapLibre GL map — controls provided by MapLibre NavigationControl --}}
        <div
            x-data="rapidaMap({
                crisisSlug: '{{ $slug }}',
                mode: '{{ $mode }}',
                center: [{{ $centerLng }}, {{ $centerLat }}],
                zoom: {{ $zoom }},
                tokens: {
                    damage_minimal: '#22c55e',   // damage-minimal-map
                    damage_partial: '#f59e0b',   // damage-partial-map
                    damage_complete: '#c46b5a',  // crisis-rose-400
                    footprint_fill: '#2e6689',   // rapida-blue-700
                    footprint_stroke: '#1a3a4a', // rapida-blue-900
                    user_dot: '#2e6689',         // rapida-blue-700
                },
                buildingsUrl: '/api/v1/crises/{{ $slug }}/buildings',
                pinsUrl: '/api/v1/crises/{{ $slug }}/pins',
                heatmapUrl: '/api/v1/crises/{{ $slug }}/heatmap',
            })"
            x-init="init()"
            wire:ignore
            id="rapida-map"
            class="absolute inset-0"
            aria-label="Interactive damage map — tap to select location"
        ></div>
    @else
        {{-- Fallback when no active crisis --}}
        <div id="rapida-map" class="absolute inset-0 bg-slate-200 flex items-center justify-center">
            <div class="text-center space-y-2">
                <x-atoms.icon name="pin" size="xl" class="text-slate-400 mx-auto" />
                <p class="text-body text-slate-500">No active crisis</p>
                <p class="text-caption text-slate-400">The map will appear when a crisis is activated</p>
            </div>
        </div>
    @endif

    {{-- Legend (reusable, translated) — bottom-2.5 aligns with MapLibre attribution baseline --}}
    <x-molecules.map-legend class="absolute bottom-2.5 left-3 z-10" />
</div>
